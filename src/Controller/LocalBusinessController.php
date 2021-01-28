<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\LocalBusiness;

class LocalBusinessController implements ControllerInterface
{
    public function index($params = null): array 
    {                   
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ];
        
        $params3 = $params ? array_merge($params2, $params) : $params2;
        
        return (new LocalBusiness())->get($params3);
    }
    
    public function edit(array $params): array 
    {
        $newParams = array_merge($params, [ "properties" => "*,location,organization,contactPoint,member,image" ]);
        
        return (new LocalBusiness())->get($newParams);
    }
    
    public function new($params = null)
    {
        return true;
    }
}
