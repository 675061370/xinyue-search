<?php

namespace app\api\controller;

use app\api\QfShop;

use Lizhichao\Word\VicWord;



class Index extends QfShop
{
    public function index()
    {
        return jok("Hello World!");
    }
    public function search() {
        $fc = new VicWord();
        $keywords = input('keywords');
        $keywords = $fc->getAutoWord($keywords);
        
        $keywords = filterAndExtractWords($keywords);
        
        
        return jok("Hello World!",$keywords);
    }
}
