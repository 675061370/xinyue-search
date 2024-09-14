<?php

namespace app\model;

use app\model\QfShop;

class Conf extends QfShop
{
    /**
     * 更新配置
     *
     * @param string 配置key
     * @param string 配置值
     * @param int 整形配置
     * @param string 配置描述
     * @return void
     */
    public function updateConf($key, $value, $int = 0, $desc = null)
    {
        $data = [];
        if ($desc) {
            $data['conf_desc'] = $desc;
        }
        $data['conf_value'] = $value;
        $this->where([
            "conf_key"    => $key,
            "conf_readonly" => 0,
        ])->update($data);
    }
}
