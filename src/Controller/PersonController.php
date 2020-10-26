<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Person;

class PersonController implements ControllerInterface
{
    public function index($params = null): array 
    {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ];
        
        $params3 = $params ? array_merge($params2, $params) : $params2;
        
        return (new Person())->get($params3);
    }
    
    public function new() 
    {
        return true;
    }
    
    public function edit(array $params): array 
    {
        $params = array_merge($params, [ "properties" => "contactPoint,address" ]);
        
        return (new Person())->get($params);
    }
}