<?php

namespace app\admin\controller;

use app\admin\QfShop;

class Error extends QfShop
{
    public function index()
    {
        return jerr("admin not found", 404);
    }
}
