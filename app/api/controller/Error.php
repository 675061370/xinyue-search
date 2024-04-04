<?php

namespace app\api\controller;

use app\api\QfShop;

class Error extends QfShop
{
    public function index()
    {
        return jerr("Error", 404);
    }
}
