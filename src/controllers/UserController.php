<?php

namespace Plinct\Cms\Controller;

class UserController implements ControllerInterface
{   
    public function index($params = null): array 
    {   
        $params = [ "format" => "ItemList" ];
        
        $data = (new \Plinct\Api\Type\User())->get($params);
        
        return $data;
    }
    
    public function edit($params): array 
    {
        $params = array_merge($params, [ "properties" => "email,create_time" ]);
        
        $data = (new \Plinct\Api\Type\User())->get($params);
        
        return $data[0];        
    }
    
    public function new() 
    {
        ;
    }
}
