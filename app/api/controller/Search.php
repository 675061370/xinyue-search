<?php

namespace app\api\controller;

use app\api\QfShop;
use app\model\Source as SourceModel;
use app\model\SourceCategory as SourceCategoryModel;

class Search extends QfShop
{
    public function index()
    {
        $SourceModel = new SourceModel();
        $data = $SourceModel->getList(input(''));
        return jok('获取成功',$data);
    }
    
    public function getDetail()
    {
        $SourceModel = new SourceModel();
        $data = $SourceModel->getDetail(input(''));
        return jok('获取成功',$data);
    }
    
    public function getNew()
    {
        $SourceModel = new SourceModel();
        $data = $SourceModel->getNew(input(''));
        return jok('获取成功',$data);
    }
    
    public function getHot()
    {
        $SourceModel = new SourceModel();
        $data = $SourceModel->getHot(input(''));
        return jok('获取成功',$data);
    }
    
    public function getCategory()
    {
        $SourceCategoryModel = new SourceCategoryModel();
        $data = $SourceCategoryModel->getList(input(''));
        return jok('获取成功',$data);
    }
}
