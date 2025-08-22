<?php

namespace netdisk\pan;

class BaiduPan extends BasePan
{

    public function getFiles($pdir_fid = 0)
    {
        if ($pdir_fid === 0) {
            $pdir_fid = '/';
        }
        $cookie = Config('qfshop.baidu_cookie');
        $network = new \netdisk\pan\BaiduWork($cookie);

        $res = $network->getDirList($pdir_fid);
        // 如果返回的是错误码，说明目录不存在，需要创建
        if (is_numeric($res)) {
            return jerr2($network->getErrorMessage($res));
        }

        return jok2('获取成功', $res);
    }

    public function transfer($pwd_id)
    {
        $cookie = Config('qfshop.baidu_cookie');
        $network = new \netdisk\pan\BaiduWork($cookie);

        $bdstoken = $network->getBdstoken();
        $network->setBdstoken($bdstoken);

        $urlParts = parse_url($this->url);
        $linkUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path']; // 分享链接
        $passCode = $this->code; // 4位提取码


        // 先判断是否有提取码
        if (!empty($passCode)) {
            // 验证提取码
            $randsk = $network->verifyPassCode($linkUrl, $passCode);
            if (is_numeric($randsk)) {
                return jerr2($network->getErrorMessage($randsk));
            }
            // 验证成功，更新 cookie
            $network->updateBdclnd($randsk);
        }

        // 获取转存参数
        $transferParams = $network->getTransferParams($linkUrl);
        if (is_numeric($transferParams)) {
            return jerr2($network->getErrorMessage($transferParams));
        }

        // 解析返回的参数
        list($shareId, $userId, $fsIds, $fileNames, $isDirs) = $transferParams;

        if ($this->isType == 1) {
            $urls['title'] = $fileNames[0];
            $urls['share_url'] = $this->url;
            return jok2('检验成功', $urls);
        }

        $folderName = Config('qfshop.baidu_file'); //默认存储路径
        if ($this->expired_type == 2) {
            $folderName = Config('qfshop.baidu_file_time'); //临时资源路径
        }

        if (empty($folderName)) {
            $folderName = '/默认转存文件';  // 未设置时默认目录
        }

        // 检查目录名是否包含非法字符
        $invalidChars = ['<', '>', '|', '*', '?', '\\', ':'];
        foreach ($invalidChars as $char) {
            if (strpos($folderName, $char) !== false) {
                return jerr2('转存目录名有非法字符，不能包含：< > | * ? \\ :');
            }
        }

        // 先检查目录是否存在，不存在再创建
        $dirList = $network->getDirList($folderName);
        // 如果返回的是错误码，说明目录不存在，需要创建
        if (is_numeric($dirList)) {
            $createResult = $network->createDir($folderName);
            if ($createResult !== 0) {
                return jerr2($network->getErrorMessage($createResult));
            }
        }

        // 执行文件转存
        $transferResult = $network->transferFile([$shareId, $userId, $fsIds], $folderName);
        if ($transferResult !== 0) {
            return jerr2($network->getErrorMessage($transferResult));
        }

        // 转存成功后，获取目录中的文件列表
        $dirList = $network->getDirList('/' . $folderName);
        if (is_numeric($dirList)) {
            return jerr2($network->getErrorMessage($dirList));
        }

        // 找到刚刚转存的所有文件
        $targetFiles = [];
        $fsIdList = [];
        $filePaths = [];
        $adFilePaths = []; // 用于存储包含广告的文件路径
        $allFilesAreAds = true; // 假设所有文件都是广告

        foreach ($dirList as $file) {
            if (in_array($file['server_filename'], $fileNames)) {
                $targetFiles[] = $file;
                $fsIdList[] = $file['fs_id'];
                $filePath = '/' . $folderName . '/' . $file['server_filename'];
                $filePaths[] = $filePath;

                // 检查文件名是否包含广告内容
                $containsAd = $this->containsAdKeywords($file['server_filename']);

                // 如果是目录，需要检查目录内的文件
                if ($file['isdir'] == 1) {
                    // 获取子目录内容
                    $subDirList = $network->getDirList($filePath);
                    if (!is_numeric($subDirList)) {
                        foreach ($subDirList as $subFile) {
                            // 检查子文件是否包含广告关键词
                            if ($this->containsAdKeywords($subFile['server_filename'])) {
                                // 将包含广告的子文件添加到待删除列表
                                $adFilePaths[] = $filePath . '/' . $subFile['server_filename'];
                            } else {
                                // 只要有一个文件不是广告，就将标志设为false
                                $allFilesAreAds = false;
                            }
                        }
                    }
                } else {
                    // 如果是文件，直接判断
                    if ($containsAd) {
                        $adFilePaths[] = $filePath;
                    } else {
                        $allFilesAreAds = false;
                    }
                }
            }
        }

        if (empty($targetFiles)) {
            return jerr2('分享失败，找不到刚转存的文件');
        }

        // 如果所有文件都是广告，删除整个转存的内容
        if ($allFilesAreAds && !empty($targetFiles)) {
            // 删除所有转存的文件
            $deleteResult = $network->batchDeleteFiles($filePaths);
            if ($deleteResult['errno'] === 0) {
                return jerr2('资源内容为空或所有转存的文件都包含广告内容，已全部删除');
            } else {
                // return jerr2('删除广告文件失败');
            }
        }

        // 如果只有部分文件是广告，只删除广告文件
        if (!empty($adFilePaths)) {
            $deleteResult = $network->batchDeleteFiles($adFilePaths);
            // 删除后更新文件列表
            if ($deleteResult['errno'] === 0) {
                // 从fsIdList和filePaths中移除已删除的广告文件
                foreach ($adFilePaths as $adPath) {
                    $key = array_search($adPath, $filePaths);
                    if ($key !== false) {
                        unset($filePaths[$key]);
                        unset($fsIdList[$key]);
                    }
                }
                // 重新索引数组
                $filePaths = array_values($filePaths);
                $fsIdList = array_values($fsIdList);
            }
        }

        // 如果删除广告后没有文件了，返回提示
        if (empty($fsIdList)) {
            return jerr2('资源内容为空或所有转存的文件都包含广告内容，已全部删除');
        }

        // 创建分享
        $expiry = 0; // 0为永久
        // $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4); // 随机4位提取码
        $password = '6666'; // 随机4位提取码
        $shareLink = $network->createShare(implode(',', $fsIdList), $expiry, $password);

        if (is_numeric($shareLink)) {
            return jerr2($network->getErrorMessage($shareLink));
        }

        if (!empty($password)) {
            $shareLink = $shareLink . '?pwd=' . $password;
        }
        // 转存成功
        return jok2("文件转存成功", [
            'title' => $fileNames[0],
            'share_url' => $shareLink,
            'fid' => $filePaths,
            'code' => $password,
        ]);
    }


    /**
     * 检查文件名是否包含广告关键词
     * @param string $filename 文件名
     * @return bool 是否包含广告关键词
     */
    private function containsAdKeywords($filename)
    {
        $banned = Config('qfshop.quark_banned') ?? ''; // 如果出现这些字样就删除

        // 广告关键词列表
        $adKeywords = [];
        if (!empty($banned)) {
            $adKeywords = array_map('trim', explode(',', $banned));
        }

        // 转为小写进行比较
        $lowercaseFilename = mb_strtolower($filename);

        foreach ($adKeywords as $keyword) {
            if (mb_strpos($lowercaseFilename, mb_strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 删除指定资源
     * 
     * @return void
     */
    public function deletepdirFid($filePaths)
    {
        $cookie = Config('qfshop.baidu_cookie');
        $network = new \netdisk\pan\BaiduWork($cookie);

        $bdstoken = $network->getBdstoken();
        $network->setBdstoken($bdstoken);

        // 调用批量删除方法
        $result = $network->batchDeleteFiles($filePaths);
        return $result;
    }
}
