<?php

namespace app\model;

use app\model\QfShop;

class SourceCategory extends QfShop
{
    /**
     * 获取列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getList(array $data)
    {

        // 搜索条件
        $map = [];
        $map[] = ['status', '=', 0];
        $result = $this->where($map)->order('sort', 'desc')->select();
        return $result;
    }
}
