<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\WebPage;
use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Type\WebPageElement;

class WebPageController implements ControllerInterface
{   
    public function index($params = null): array 
    {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ], $params);
        
        return (new WebPage())->get($params2);
    }
    
    public function edit(array $params): array 
    {
        $params2 = array_merge($params, [ "properties" => "" ]);
        
        $webPageData = (new WebPage())->get($params2);        
        $response['webPage'] = $webPageData[0];
        $idwebPage = PropertyValue::extractValue($response['webPage']['identifier'], 'id');
        
        // WebPageElement
        $response['webPageElement'] = (new WebPageElement())->get([ "idwebPage" => $idwebPage, "format" => "ItemList", "properties" => "image" ]);
        
        return $response;
    }
    
    public function new() 
    {
        return true;
    }
}
