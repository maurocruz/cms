<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\WebPage;

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
}
