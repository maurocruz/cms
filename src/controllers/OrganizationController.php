<?php

namespace Plinct\Cms\Controller;

class OrganizationController implements ControllerInterface
{
    
    public function index(): array 
    { 
        $params = [ "format" => "ItemList" ];
        return (new \Plinct\Api\Type\Organization())->get($params);
    }
    
    public function edit(array $params): array 
    {   
        $params = [ "id" => $params['id'], "properties" => "additionalType,url,address,contactPoint,member,location,image" ];
        
        return (new \Plinct\Api\Type\Organization())->get($params);
    }
    
    public function new() 
    {
        return true;
    }
}
