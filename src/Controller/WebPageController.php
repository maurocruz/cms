<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\WebPage;
use Plinct\Tool\Sitemap;

class WebPageController implements ControllerInterface
{   
    public function index($params = null): array 
    {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ], $params);
        
        return (new WebPage())->get($params2);
    }
    
    public function edit(array $params): array 
    {
        $params2 = array_merge($params, [ "properties" => "alternativeHeadline,hasPart" ]);
        
        return (new WebPage())->get($params2);
    }
    
    public function new() 
    {
        return true;
    }

    public function saveSitemap()
    {
        $dataSitemap = null;

        $data = (new WebPage())->get([]);

        foreach ($data as $value) {
            $dataSitemap[] = [
                "loc" => "http://" . filter_input(INPUT_SERVER, 'HTTP_HOST').$value['url'],
            ];
        }

        (new Sitemap("sitemap.xml"))->createSitemap($dataSitemap);
    }
}
