<?php

namespace app\index\controller;

use think\Response;
use think\facade\Request;

use app\model\Source as SourceModel;


class Sitemap
{ 
    public function index()
    {
        // 检查是否有缓存
        $sitemap = cache('sitemap');
        
        if (!$sitemap) {
            $SourceModel = new SourceModel();
            
            $map[] = ['status', '=', 1];
            $map[] = ['is_delete', '=', 0];
            $map[] = ['is_time', '=', 0];
            
            $urls = $SourceModel->where($map)->field('source_id, update_time')->order('update_time', 'desc')->limit(10000)->select();
            
            // 创建 XML 内容
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    
            $xml .= '<url>';
            $xml .= '<loc>'.Request::domain().'</loc>'; // 网站 URL
            $xml .= '<lastmod>'.date('Y-m-d').'</lastmod>'; // 最后修改时间
            $xml .= '<changefreq>daily</changefreq>'; // 页面更新频率
            $xml .= '<priority>1.0</priority>'; // 优先级
            $xml .= '</url>';
            
            
            foreach ($urls as $url) {
                $xml .= '<url>';
                $xml .= '<loc>'.Request::domain().'/d/'.$url['source_id'].'.html</loc>';
                $xml .= '<lastmod>' . date('Y-m-d', strtotime($url['update_time'])) . '</lastmod>';
                $xml .= '<priority>0.8</priority>';
                $xml .= '</url>';
            }
        
            $xml .= '</urlset>';
            
            // 缓存 sitemap 1 小时 3600
            cache('sitemap', $xml, 86400);
    
            $sitemap = $xml;
        }
        return Response::create($sitemap, 'xml')->header(['Content-Type' => 'application/xml']);
    }
}
