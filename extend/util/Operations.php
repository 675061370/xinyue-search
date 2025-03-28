<?php
namespace util;

class Operations
{
    protected $network;
    protected $cookie;
    protected $folderName;
    protected $linkList;
    protected $linkListOrg;
    protected $totalTaskCount;
    protected $completedTaskCount;
    protected $running;
    protected $customMode;
    protected $checkMode;
    protected $dirListAll;

    const SAVE_LIMIT = 1000;
    const DELAY_SECONDS = 0.1;
    const INVALID_CHARS = ['<', '>', '|', '*', '?', '\\', ':'];

    public function __construct($cookie = '')
    {
        $this->network = new Network($cookie);
        $this->cookie = $cookie;
        $this->completedTaskCount = 0;
        $this->running = true;
    }

    public function save($links, $folderName = '', $customMode = false, $checkMode = false)
    {
        try {
            $this->prepareRun($folderName, $customMode, $checkMode);
            $this->setupSave($links);
            $this->handleInput();
            $this->handleBdstoken();
            $this->handleCreateDir($this->folderName);
            $this->handleProcessSave();
            return ['code' => 0, 'msg' => '转存完成'];
        } catch (\Exception $e) {
            return ['code' => -1, 'msg' => '程序出现未预料错误: ' . $e->getMessage()];
        }
    }

    protected function prepareRun($folderName, $customMode, $checkMode)
    {
        $this->folderName = trim($folderName);
        $this->customMode = $customMode;
        $this->checkMode = $checkMode;
        $this->completedTaskCount = 0;
    }

    protected function setupSave($links)
    {
        $this->linkList = array_filter(array_map(function($link) {
            return $this->normalizeLink($link . ' ');
        }, explode("\n", $links)));
        
        $this->linkListOrg = array_unique(array_filter(explode("\n", $links)));
        $this->totalTaskCount = count($this->linkList);
    }

    protected function handleInput()
    {
        if ($this->totalTaskCount == 0) {
            throw new \Exception('无有效链接。');
        }
        if ($this->totalTaskCount > self::SAVE_LIMIT) {
            throw new \Exception("批量转存一次不能超过 " . self::SAVE_LIMIT . "，当前链接数：" . $this->totalTaskCount);
        }
        if (!preg_match('/^[\x00-\x7F]+$/', $this->cookie) || strpos($this->cookie, 'BAIDUID') === false) {
            throw new \Exception('百度网盘 cookie 输入不正确。');
        }
        foreach (self::INVALID_CHARS as $char) {
            if (strpos($this->folderName, $char) !== false) {
                throw new \Exception('转存目录名有非法字符，不能包含：< > | * ? \ :');
            }
        }
    }

    protected function handleBdstoken()
    {
        $bdstoken = $this->network->getBdstoken();
        if (is_numeric($bdstoken)) {
            throw new \Exception("没获取到 bdstoken 参数，错误代码：" . $bdstoken);
        }
        $this->network->setBdstoken($bdstoken);
    }

    protected function handleCreateDir($folderName)
    {
        if (!empty($folderName)) {
            $result = $this->network->getDirList("/{$folderName}");
            if (is_numeric($result)) {
                $returnCode = $this->network->createDir($folderName);
                if ($returnCode !== 0) {
                    throw new \Exception("创建目录失败，错误代码：" . $returnCode);
                }
            }
        }
    }

    protected function handleProcessSave()
    {
        $results = [];
        foreach ($this->linkList as $urlCode) {
            $result = $this->processSave($urlCode);
            $results[] = $result;
            usleep(self::DELAY_SECONDS * 1000000);
        }
        return $results;
    }

    protected function processSave($urlCode)
    {
        if (strpos($urlCode, 'https://pan.baidu.com/') === false) {
            return ['status' => 'error', 'message' => "不支持的链接：{$urlCode}"];
        }

        list($url, $code) = $this->parseUrlAndCode($urlCode);
        $result = $this->verifyLink($url, $code);

        if ($this->checkMode) {
            return $this->checkOnly($result, $urlCode);
        }

        return $this->saveFile($result, $urlCode, $this->folderName);
    }

    protected function normalizeLink($link)
    {
        if (empty($link)) return '';
        
        $link = trim($link);
        if (preg_match('/https:\/\/pan\.baidu\.com\/s\/[a-zA-Z0-9_-]+/', $link, $matches)) {
            return $matches[0];
        }
        return '';
    }

    protected function parseUrlAndCode($urlCode)
    {
        $parts = explode(' ', trim($urlCode));
        $url = $parts[0];
        $code = isset($parts[1]) ? $parts[1] : '';
        return [$url, $code];
    }

    protected function verifyLink($url, $password)
    {
        if ($password) {
            $bdclnd = $this->network->verifyPassCode($url, $password);
            if (is_numeric($bdclnd)) {
                return $bdclnd;
            }
        }
        
        $response = $this->network->getTransferParams($url);
        return $this->parseResponse($response);
    }

    protected function parseResponse($html)
    {
        // 实现解析响应的逻辑
        // 返回解析后的参数数组或错误代码
        return [];
    }

    protected function checkOnly($result, $urlCode)
    {
        if (is_array($result)) {
            return ['status' => 'success', 'message' => "链接有效：{$urlCode}"];
        }
        return ['status' => 'error', 'message' => "链接无效：{$urlCode}"];
    }

    protected function saveFile($result, $urlCode, $folderName)
    {
        if (!is_array($result)) {
            return ['status' => 'error', 'message' => "转存失败：{$urlCode}"];
        }

        if ($this->customMode) {
            $folderName = $this->createUserDir($folderName);
        }

        $transferResult = $this->network->transferFile($result, $folderName);
        
        if ($transferResult === 0) {
            return ['status' => 'success', 'message' => "转存成功：{$urlCode} 到 {$folderName}"];
        }
        
        return ['status' => 'error', 'message' => "转存失败，错误代码({$transferResult})：{$urlCode}"];
    }

    protected function createUserDir($baseFolderName)
    {
        if (empty($baseFolderName)) {
            throw new \Exception('必须输入转存目录');
        }

        $customFolder = $this->completedTaskCount + 1;
        $folderName = $baseFolderName . '/' . $customFolder;
        
        // 替换非法字符
        $folderName = str_replace(self::INVALID_CHARS, '_', $folderName);
        
        $this->handleCreateDir($folderName);
        return $folderName;
    }
}