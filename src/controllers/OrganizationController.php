<?php

namespace Plinct\Cms\Controller;

class OrganizationController implements ControllerInterface
{
    public function index($params = null): array 
    { 
        $paramsSet = [ "format" => "ItemList", "properties" => "update_time", "orderBy" => "update_time", "ordering" => "desc" ];
        
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        
        return (new \Plinct\Api\Type\Organization())->get($paramsGet);
    }
    
    public function edit(array $params): array 
    {   
        $params = [ "id" => $params['id'], "properties" => "*,contactPoint,member,image" ];
        
        return (new \Plinct\Api\Type\Organization())->get($params);
    }
    
    public function new() 
    {
        return true;
    }
}
