<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Organization;

class OrganizationController implements ControllerInterface
{
    public function index($params = null): array 
    { 
        $paramsSet = [ "format" => "ItemList", "properties" => "update_time", "orderBy" => "update_time", "ordering" => "desc" ];
        
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        
        return (new Organization())->get($paramsGet);
    }
    
    public function edit(array $params): array 
    {   
        $params = [ "id" => $params['id'], "properties" => "*,address,location,contactPoint,member,image" ];
        
        return (new Organization())->get($params);
    }
    
    public function new($params = null): bool
    {
        return true;
    }
}
