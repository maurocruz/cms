<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\LocalBusiness;

class LocalBusinessController implements ControllerInterface
{
    public function index($params = null): array 
    {   
        $search = filter_input(INPUT_GET, "q");
        
        $params = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ];                
        
        $params2 = $search ? array_merge($params, [ "where" => "`name` LIKE '%$search%'" ]) : $params;
        
        return (new LocalBusiness())->get($params2);
    }
    
    public function edit(array $params): array 
    {
        $newParams = array_merge($params, [ "properties" => "location,contactPoint,member,image" ]);
        
        return (new LocalBusiness())->get($newParams);
    }
    
    public function new() 
    {
        return true;
    }
}
