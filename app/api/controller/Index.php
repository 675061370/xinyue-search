<?php

namespace app\api\controller;

use app\api\QfShop;

use Lizhichao\Word\VicWord;



class Index extends QfShop
{
    public function index()
    {
        // return jok("Hello World!");

        $cookie = 'csrfToken=gKesaboeWtJkukG4VNGh5Ljk; BAIDUID=757FBF0C282638B49A59DC7E37C7702B:FG=1; BAIDUID_BFESS=757FBF0C282638B49A59DC7E37C7702B:FG=1; Hm_lvt_7a3960b6f067eb0085b7f96ff5e660b0=1742787964; RT="z=1&dm=baidu.com&si=86a18d7d-4537-4b9b-ac36-06a756df3179&ss=m8mj80dn&sl=0&tt=0&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ul=261&hd=26a"; Hm_lpvt_7a3960b6f067eb0085b7f96ff5e660b0=1742794455; newlogin=1; ppfuid=FOCoIC3q5fKa8fgJnwzbE67EJ49BGJeplOzf+4l4EOvDuu2RXBRv6R3A1AZMa49I27C0gDDLrJyxcIIeAeEhD8JYsoLTpBiaCXhLqvzbzmvy3SeAW17tKgNq/Xx+RgOdb8TWCFe62MVrDTY6lMf2GrfqL8c87KLF2qFER3obJGmxOaJD7Qr04D9rET96PX99GEimjy3MrXEpSuItnI4KD2P5vWa8VVdqKPLBckQ0WyruzFB5pZ7L1GIDHy291nRZSc37WI7hn7N5DEkitWgHVHqxGUGRl1qke9+4QxQVI1jGgLbz7OSojK1zRbqBESR5Pdk2R9IA3lxxOVzA+Iw1TWLSgWjlFVG9Xmh1+20oPSbrzvDjYtVPmZ+9/6evcXmhcO1Y58MgLozKnaQIaLfWRIM4pp9u1B7t2Y8SxQH/XnrgZXt2Kg4R5SS0He7SlWGt42bJBOW2wZKr1YF6Z6VWTM5FjnYxYstXg/9EfB3EVmLB2thKqX6G/zUWgdr9REaklV1Uhhp5FAe6gNJIUptp7EMAaXYKm11G+JVPszQFdp9AJLcm4YSsYUXkaPI2Tl66J246cmjWQDTahAOINR5rXR5r/7VVI1RMZ8gb40q7az7vCK56XLooKT5a+rsFrf5Zu0yyCiiagElhrTEOtNdBJJq8eHwEHuFBni9ahSwpC7lbKkUwaKH69tf0DFV7hJROiLETSFloIVkHdy3+I2JUr1LsplAz0hMkWt/tE4tXVUV7QcTDTZWS/2mCoS/GV3N9awQ6iM6hs/BWjlgnEa1+5hYkkYOBBLZqDwzG9FQyZZJmSCAynpprqnVbhrjcNYdk5FKazXcZ/j40FJv+iLGBn3nkkgHlne61I8I7KhtQgInXjsNS9+mteGCT/vj/pEvkm+ZitcePbf21QMldRrlvw4uFnWmLU84Th29MqUuVR0ujl0ys3zTnxv/D47Q2VFyeFv12nptBFm2PISy0WmJ/Sy3ktTWpm7mcOABi5Gey1FBzkrq/nzqRQID/LRsJJW24e+1N4wlJzhNmFXMQ0YiYVdq7aHN89Oybut+xsc38J6I4Wd2rVi5xrH8hGqvd77OJuzH8X+tb0PV+Xnu3NL1fmqLIh+XcF6fnyPFPUxteQtbtLyi+gq5zowg1oFj8O/L9oVsoK22a9qUmM/HrJMRsLi1+J9aSd42+X78fDIZgkPh3epzLLvwRmnAbs5z/V+jl3P3gVnlwm9bfwhaFtnhFN2dHYAw7i4QhrXdzc77isXhbvkM5DsEwz5RTVSb4C+N+kIl81Iase/C16XVPKOj9XA==; BDUSS=jYwbDRJZG0yTi1VbDgtMWF6M1BGZ0k0ZG96dm9iOHJGczFMOUYtMFhwYlJmd2hvRVFBQUFBJCQAAAAAAAAAAAEAAAA63UdY2K~Yr8Dksru5~cjL0MQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANHy4GfR8uBnT; BDUSS_BFESS=jYwbDRJZG0yTi1VbDgtMWF6M1BGZ0k0ZG96dm9iOHJGczFMOUYtMFhwYlJmd2hvRVFBQUFBJCQAAAAAAAAAAAEAAAA63UdY2K~Yr8Dksru5~cjL0MQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANHy4GfR8uBnT; STOKEN=608a270497995724e6ed69015178372081331f404747f0a8492ee31de1184c41; PANPSC=6332961014215215224%3AAuI6D6iGxsvXU%2FMlrspYA1cS2d9ns3O5g0mIZdLHpdQGbqupDlB1gtH1%2BL8BUDk1coDRJu%2FbTp%2FiLaLUl0KD5De4ZdaW7CoOlL98c8Ccr9ch6uZoP3DwQ9YfJggg9xZJa6gjWHNi7jj6tFSlPcYsyyZWmuM7JILl4szUrnVKA5MNNiO7%2B4TS%2B1QskbZnP5EyWagh%2FyTVtumv9EBTj3cIccvsqRsDSpjMTItbmssa8FDJel1POICqciYu0WtaRbrkbSBvzd4TSG3wk8UVEJrJjetuQFScyIV%2FkrFWWqsgPEw%3D; Hm_lvt_182d6d59474cf78db37e0b2248640ea5=1742788120; ndut_fmt=6F438330EB0B4C594C515F276A3E73E3F2D3A8BB5A3C26DD1E721E98B58ED17B; Hm_lpvt_182d6d59474cf78db37e0b2248640ea5=1742795476; ab_sr=1.0.1_YTI3MjJmMjkwY2Y5MTJhMjc4MGJhNWU2ZGMwNTFiZWZiNmZhMTZkMWRlNWYyMzAxNTA1OTU2YTU4ZjU5M2ZiNWI5NDY2YjYwZWY3Yjc4YTY3MmNiMWVlNWNkNzE2ZGI2YzJmYTY1YTgwM2M1ZDJlYTk1YjAxZTcyMmNkNWRkNzliM2RiOGMzYzUxYmMwZTU4OTJiZmRmNjhiMDIzZDc1OWRhMGIyNGNkMDJhOTMzZjllYmQxMDUzY2ZjYzU4MjVk';
        // $cookie = '';
        $network = new \netdisk\pan\BaiduWork($cookie);
        $bdstoken = $network->getBdstoken();
        $network->setBdstoken($bdstoken);

        // 验证提取码
        // $linkUrl = "https://pan.baidu.com/s/11ioAjAwdwzI3BEvP5c62bw"; // 分享链接
        // $passCode = "him8"; // 4位提取码

        $linkUrl = "https://pan.baidu.com/s/1HU99KPtdJEpKnYnYLnQ37Q"; // 分享链接
        $passCode = "ruwf"; // 4位提取码

        // $linkUrl = "https://pan.baidu.com/s/1f03pM6dvtZPGA1bA0DH9AQ"; // 分享链接
        // $passCode = "a9oa"; // 4位提取码

        // 先判断是否有提取码
        if (!empty($passCode)) {
            // 验证提取码
            $randsk = $network->verifyPassCode($linkUrl, $passCode);
            if (is_numeric($randsk)) {
                return jerr($network->getErrorMessage($randsk));
            }
            // 验证成功，更新 cookie
            $network->updateBdclnd($randsk);
        }

        // 获取转存参数
        $transferParams = $network->getTransferParams($linkUrl);
        if (is_numeric($transferParams)) {
            return jerr($network->getErrorMessage($transferParams));
        }
        // 解析返回的参数
        list($shareId, $userId, $fsIds, $fileNames, $isDirs) = $transferParams;

        $folderName = '';  // 自定义保存目录名
        // 检查目录名是否包含非法字符
        $invalidChars = ['<', '>', '|', '*', '?', '\\', ':'];
        foreach ($invalidChars as $char) {
            if (strpos($folderName, $char) !== false) {
                return jerr('转存目录名有非法字符，不能包含：< > | * ? \\ :');
            }
        }

        // 先检查目录是否存在，不存在再创建
        $dirList = $network->getDirList('/' . $folderName);
        // 如果返回的是错误码，说明目录不存在，需要创建
        if (is_numeric($dirList)) {
            $createResult = $network->createDir($folderName);
            if ($createResult !== 0) {
                return jerr($network->getErrorMessage($createResult));
            }
        }

        // 执行文件转存
        $transferResult = $network->transferFile([$shareId, $userId, $fsIds], $folderName);
        if ($transferResult !== 0) {
            return jerr($network->getErrorMessage($transferResult));
        }

        // 转存成功后，获取目录中的文件列表
        $dirList = $network->getDirList('/' . $folderName);
        if (is_numeric($dirList)) {
            return jerr($network->getErrorMessage($dirList));
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
            return jerr('找不到刚转存的文件');
        }
        
        // 如果所有文件都是广告，删除整个转存的内容
        if ($allFilesAreAds && !empty($targetFiles)) {
            // 删除所有转存的文件
            $deleteResult = $network->batchDeleteFiles($filePaths);
            if ($deleteResult['errno'] === 0) {
                return jok('所有转存的文件都包含广告内容，已全部删除', [
                    'deletedCount' => $deleteResult['deletedCount'],
                    'paths' => $deleteResult['paths']
                ]);
            } else {
                return jerr('删除广告文件失败: ' . $deleteResult['message']);
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
            return jok('所有有效文件都包含广告内容，已全部删除');
        }
        
        // 创建分享
        $expiry = 0; // 0为永久
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 4); // 随机4位提取码
        $shareLink = $network->createShare(implode(',', $fsIdList), $expiry, $password);

        if (is_numeric($shareLink)) {
            return jerr($network->getErrorMessage($shareLink));
        }

        // 转存成功
        return jok("文件转存成功", [
            'fileNames' => $fileNames,
            'fileCount' => count($fsIdList),
            'folderName' => $folderName,
            'shareLink' => $shareLink,
            'password' => $password,
            'filePaths' => $filePaths,
            'removedAdFiles' => $adFilePaths
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

    public function getFiles()
    {
        $cookie = 'csrfToken=gKesaboeWtJkukG4VNGh5Ljk; BAIDUID=757FBF0C282638B49A59DC7E37C7702B:FG=1; BAIDUID_BFESS=757FBF0C282638B49A59DC7E37C7702B:FG=1; Hm_lvt_7a3960b6f067eb0085b7f96ff5e660b0=1742787964; RT="z=1&dm=baidu.com&si=86a18d7d-4537-4b9b-ac36-06a756df3179&ss=m8mj80dn&sl=0&tt=0&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ul=261&hd=26a"; Hm_lpvt_7a3960b6f067eb0085b7f96ff5e660b0=1742794455; newlogin=1; ppfuid=FOCoIC3q5fKa8fgJnwzbE67EJ49BGJeplOzf+4l4EOvDuu2RXBRv6R3A1AZMa49I27C0gDDLrJyxcIIeAeEhD8JYsoLTpBiaCXhLqvzbzmvy3SeAW17tKgNq/Xx+RgOdb8TWCFe62MVrDTY6lMf2GrfqL8c87KLF2qFER3obJGmxOaJD7Qr04D9rET96PX99GEimjy3MrXEpSuItnI4KD2P5vWa8VVdqKPLBckQ0WyruzFB5pZ7L1GIDHy291nRZSc37WI7hn7N5DEkitWgHVHqxGUGRl1qke9+4QxQVI1jGgLbz7OSojK1zRbqBESR5Pdk2R9IA3lxxOVzA+Iw1TWLSgWjlFVG9Xmh1+20oPSbrzvDjYtVPmZ+9/6evcXmhcO1Y58MgLozKnaQIaLfWRIM4pp9u1B7t2Y8SxQH/XnrgZXt2Kg4R5SS0He7SlWGt42bJBOW2wZKr1YF6Z6VWTM5FjnYxYstXg/9EfB3EVmLB2thKqX6G/zUWgdr9REaklV1Uhhp5FAe6gNJIUptp7EMAaXYKm11G+JVPszQFdp9AJLcm4YSsYUXkaPI2Tl66J246cmjWQDTahAOINR5rXR5r/7VVI1RMZ8gb40q7az7vCK56XLooKT5a+rsFrf5Zu0yyCiiagElhrTEOtNdBJJq8eHwEHuFBni9ahSwpC7lbKkUwaKH69tf0DFV7hJROiLETSFloIVkHdy3+I2JUr1LsplAz0hMkWt/tE4tXVUV7QcTDTZWS/2mCoS/GV3N9awQ6iM6hs/BWjlgnEa1+5hYkkYOBBLZqDwzG9FQyZZJmSCAynpprqnVbhrjcNYdk5FKazXcZ/j40FJv+iLGBn3nkkgHlne61I8I7KhtQgInXjsNS9+mteGCT/vj/pEvkm+ZitcePbf21QMldRrlvw4uFnWmLU84Th29MqUuVR0ujl0ys3zTnxv/D47Q2VFyeFv12nptBFm2PISy0WmJ/Sy3ktTWpm7mcOABi5Gey1FBzkrq/nzqRQID/LRsJJW24e+1N4wlJzhNmFXMQ0YiYVdq7aHN89Oybut+xsc38J6I4Wd2rVi5xrH8hGqvd77OJuzH8X+tb0PV+Xnu3NL1fmqLIh+XcF6fnyPFPUxteQtbtLyi+gq5zowg1oFj8O/L9oVsoK22a9qUmM/HrJMRsLi1+J9aSd42+X78fDIZgkPh3epzLLvwRmnAbs5z/V+jl3P3gVnlwm9bfwhaFtnhFN2dHYAw7i4QhrXdzc77isXhbvkM5DsEwz5RTVSb4C+N+kIl81Iase/C16XVPKOj9XA==; BDUSS=jYwbDRJZG0yTi1VbDgtMWF6M1BGZ0k0ZG96dm9iOHJGczFMOUYtMFhwYlJmd2hvRVFBQUFBJCQAAAAAAAAAAAEAAAA63UdY2K~Yr8Dksru5~cjL0MQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANHy4GfR8uBnT; BDUSS_BFESS=jYwbDRJZG0yTi1VbDgtMWF6M1BGZ0k0ZG96dm9iOHJGczFMOUYtMFhwYlJmd2hvRVFBQUFBJCQAAAAAAAAAAAEAAAA63UdY2K~Yr8Dksru5~cjL0MQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANHy4GfR8uBnT; STOKEN=608a270497995724e6ed69015178372081331f404747f0a8492ee31de1184c41; PANPSC=6332961014215215224%3AAuI6D6iGxsvXU%2FMlrspYA1cS2d9ns3O5g0mIZdLHpdQGbqupDlB1gtH1%2BL8BUDk1coDRJu%2FbTp%2FiLaLUl0KD5De4ZdaW7CoOlL98c8Ccr9ch6uZoP3DwQ9YfJggg9xZJa6gjWHNi7jj6tFSlPcYsyyZWmuM7JILl4szUrnVKA5MNNiO7%2B4TS%2B1QskbZnP5EyWagh%2FyTVtumv9EBTj3cIccvsqRsDSpjMTItbmssa8FDJel1POICqciYu0WtaRbrkbSBvzd4TSG3wk8UVEJrJjetuQFScyIV%2FkrFWWqsgPEw%3D; Hm_lvt_182d6d59474cf78db37e0b2248640ea5=1742788120; ndut_fmt=6F438330EB0B4C594C515F276A3E73E3F2D3A8BB5A3C26DD1E721E98B58ED17B; Hm_lpvt_182d6d59474cf78db37e0b2248640ea5=1742795476; ab_sr=1.0.1_YTI3MjJmMjkwY2Y5MTJhMjc4MGJhNWU2ZGMwNTFiZWZiNmZhMTZkMWRlNWYyMzAxNTA1OTU2YTU4ZjU5M2ZiNWI5NDY2YjYwZWY3Yjc4YTY3MmNiMWVlNWNkNzE2ZGI2YzJmYTY1YTgwM2M1ZDJlYTk1YjAxZTcyMmNkNWRkNzliM2RiOGMzYzUxYmMwZTU4OTJiZmRmNjhiMDIzZDc1OWRhMGIyNGNkMDJhOTMzZjllYmQxMDUzY2ZjYzU4MjVk';
        // $cookie = '';
        $network = new \netdisk\pan\BaiduWork($cookie);

        $res = $network->getDirList('/');
        
        return jok('获取成功',$res);
    }

    public function delete()
    {
        $cookie = 'csrfToken=gKesaboeWtJkukG4VNGh5Ljk; BAIDUID=757FBF0C282638B49A59DC7E37C7702B:FG=1; BAIDUID_BFESS=757FBF0C282638B49A59DC7E37C7702B:FG=1; Hm_lvt_7a3960b6f067eb0085b7f96ff5e660b0=1742787964; RT="z=1&dm=baidu.com&si=86a18d7d-4537-4b9b-ac36-06a756df3179&ss=m8mj80dn&sl=0&tt=0&bcn=https%3A%2F%2Ffclog.baidu.com%2Flog%2Fweirwood%3Ftype%3Dperf&ul=261&hd=26a"; Hm_lpvt_7a3960b6f067eb0085b7f96ff5e660b0=1742794455; newlogin=1; ppfuid=FOCoIC3q5fKa8fgJnwzbE67EJ49BGJeplOzf+4l4EOvDuu2RXBRv6R3A1AZMa49I27C0gDDLrJyxcIIeAeEhD8JYsoLTpBiaCXhLqvzbzmvy3SeAW17tKgNq/Xx+RgOdb8TWCFe62MVrDTY6lMf2GrfqL8c87KLF2qFER3obJGmxOaJD7Qr04D9rET96PX99GEimjy3MrXEpSuItnI4KD2P5vWa8VVdqKPLBckQ0WyruzFB5pZ7L1GIDHy291nRZSc37WI7hn7N5DEkitWgHVHqxGUGRl1qke9+4QxQVI1jGgLbz7OSojK1zRbqBESR5Pdk2R9IA3lxxOVzA+Iw1TWLSgWjlFVG9Xmh1+20oPSbrzvDjYtVPmZ+9/6evcXmhcO1Y58MgLozKnaQIaLfWRIM4pp9u1B7t2Y8SxQH/XnrgZXt2Kg4R5SS0He7SlWGt42bJBOW2wZKr1YF6Z6VWTM5FjnYxYstXg/9EfB3EVmLB2thKqX6G/zUWgdr9REaklV1Uhhp5FAe6gNJIUptp7EMAaXYKm11G+JVPszQFdp9AJLcm4YSsYUXkaPI2Tl66J246cmjWQDTahAOINR5rXR5r/7VVI1RMZ8gb40q7az7vCK56XLooKT5a+rsFrf5Zu0yyCiiagElhrTEOtNdBJJq8eHwEHuFBni9ahSwpC7lbKkUwaKH69tf0DFV7hJROiLETSFloIVkHdy3+I2JUr1LsplAz0hMkWt/tE4tXVUV7QcTDTZWS/2mCoS/GV3N9awQ6iM6hs/BWjlgnEa1+5hYkkYOBBLZqDwzG9FQyZZJmSCAynpprqnVbhrjcNYdk5FKazXcZ/j40FJv+iLGBn3nkkgHlne61I8I7KhtQgInXjsNS9+mteGCT/vj/pEvkm+ZitcePbf21QMldRrlvw4uFnWmLU84Th29MqUuVR0ujl0ys3zTnxv/D47Q2VFyeFv12nptBFm2PISy0WmJ/Sy3ktTWpm7mcOABi5Gey1FBzkrq/nzqRQID/LRsJJW24e+1N4wlJzhNmFXMQ0YiYVdq7aHN89Oybut+xsc38J6I4Wd2rVi5xrH8hGqvd77OJuzH8X+tb0PV+Xnu3NL1fmqLIh+XcF6fnyPFPUxteQtbtLyi+gq5zowg1oFj8O/L9oVsoK22a9qUmM/HrJMRsLi1+J9aSd42+X78fDIZgkPh3epzLLvwRmnAbs5z/V+jl3P3gVnlwm9bfwhaFtnhFN2dHYAw7i4QhrXdzc77isXhbvkM5DsEwz5RTVSb4C+N+kIl81Iase/C16XVPKOj9XA==; BDUSS=jYwbDRJZG0yTi1VbDgtMWF6M1BGZ0k0ZG96dm9iOHJGczFMOUYtMFhwYlJmd2hvRVFBQUFBJCQAAAAAAAAAAAEAAAA63UdY2K~Yr8Dksru5~cjL0MQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANHy4GfR8uBnT; BDUSS_BFESS=jYwbDRJZG0yTi1VbDgtMWF6M1BGZ0k0ZG96dm9iOHJGczFMOUYtMFhwYlJmd2hvRVFBQUFBJCQAAAAAAAAAAAEAAAA63UdY2K~Yr8Dksru5~cjL0MQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANHy4GfR8uBnT; STOKEN=608a270497995724e6ed69015178372081331f404747f0a8492ee31de1184c41; PANPSC=6332961014215215224%3AAuI6D6iGxsvXU%2FMlrspYA1cS2d9ns3O5g0mIZdLHpdQGbqupDlB1gtH1%2BL8BUDk1coDRJu%2FbTp%2FiLaLUl0KD5De4ZdaW7CoOlL98c8Ccr9ch6uZoP3DwQ9YfJggg9xZJa6gjWHNi7jj6tFSlPcYsyyZWmuM7JILl4szUrnVKA5MNNiO7%2B4TS%2B1QskbZnP5EyWagh%2FyTVtumv9EBTj3cIccvsqRsDSpjMTItbmssa8FDJel1POICqciYu0WtaRbrkbSBvzd4TSG3wk8UVEJrJjetuQFScyIV%2FkrFWWqsgPEw%3D; Hm_lvt_182d6d59474cf78db37e0b2248640ea5=1742788120; ndut_fmt=6F438330EB0B4C594C515F276A3E73E3F2D3A8BB5A3C26DD1E721E98B58ED17B; Hm_lpvt_182d6d59474cf78db37e0b2248640ea5=1742795476; ab_sr=1.0.1_YTI3MjJmMjkwY2Y5MTJhMjc4MGJhNWU2ZGMwNTFiZWZiNmZhMTZkMWRlNWYyMzAxNTA1OTU2YTU4ZjU5M2ZiNWI5NDY2YjYwZWY3Yjc4YTY3MmNiMWVlNWNkNzE2ZGI2YzJmYTY1YTgwM2M1ZDJlYTk1YjAxZTcyMmNkNWRkNzliM2RiOGMzYzUxYmMwZTU4OTJiZmRmNjhiMDIzZDc1OWRhMGIyNGNkMDJhOTMzZjllYmQxMDUzY2ZjYzU4MjVk';
        // $cookie = '';
        $network = new \netdisk\pan\BaiduWork($cookie);
        $bdstoken = $network->getBdstoken();
        $network->setBdstoken($bdstoken);
        
        // 使用固定的文件路径数组
        $filePaths = [
            "//1",
            "//2"
        ];

        // 调用批量删除方法
        $result = $network->batchDeleteFiles($filePaths);
        
        if ($result['errno'] === 0) {
            return jok('文件删除成功', [
                'deletedCount' => $result['deletedCount'],
                'paths' => $result['paths']
            ]);
        } else {
            return jerr($result['message']);
        }
    }

    public function search() {
        $fc = new VicWord();
        $keywords = input('keywords');
        $keywords = $fc->getAutoWord($keywords);
        
        $keywords = filterAndExtractWords($keywords);
        
        
        return jok("Hello World!",$keywords);
    }
}
