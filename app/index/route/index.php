<?php


use think\facade\Route;


Route::get('s/<name>-<page?>-<cate?>', 'index/index/list')->pattern(['name' => '[^-]+', 'id' => '\d+', 'cate' => '\d+']);
Route::get('d/:id','index/index/detail');
Route::get('sitemap.xml', 'index/sitemap/index');



 