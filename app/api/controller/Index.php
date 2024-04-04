<?php

namespace app\api\controller;

use app\api\QfShop;

class Index extends QfShop
{
    public function index()
    {
        return jok("Hello World!");
    }
    public function search() {
        return jok("Hello World!");
    }
}
