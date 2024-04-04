<?php

namespace app\model;

use app\model\QfShop;

class SourceLog extends QfShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'source_log_id';

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
        'source_log_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'source_log_id'    => 'integer',
        'end_time'  =>  'timestamp',
    ];

    public function addLog($name="任务名称",$total_num=0)
    {
        try {
            $Log = [
                'name' => $name, 
                'total_num' => $total_num,
                'create_time' => time(),
                'update_time' => time(),
            ];
            $logId = $this->insertGetId($Log);
            return $logId;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function editLog($source_log_id,$total_num,$edit_name,$fail_dec='',$type=0)
    {
        try {
            $data = [];
            $data['total_num'] = $total_num;
            if(!empty($fail_dec)){
                $data['fail_dec'] = $fail_dec;
            }
            $data['update_time'] = time();
            if($type==3){
                $this->where('source_log_id', $source_log_id)
                ->update(['end_time' => time()]);
            }else{
                if($type==1){
                    $data['end_time'] = time();
                }
                $this->where('source_log_id', $source_log_id)
                ->inc($edit_name)
                ->update($data);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }
}
