<?php

namespace app\model;

use app\model\QfShop;

class Log extends QfShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'id'    => 'integer',
    ];
}
