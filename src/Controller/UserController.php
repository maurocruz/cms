<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\User;

class UserController implements ControllerInterface
{   
    public function index($params = null): array 
    {   
        $params = [ "format" => "ItemList" ];

        return (new User())->get($params);
    }
    
    public function edit($params): array 
    {
        $params = array_merge($params, [ "properties" => "email,create_time" ]);
        
        $data = (new User())->get($params);
        
        return $data[0];        
    }
    
    public function new() 
    {
        return null;
    }

    public static function getStatusWithText($status): string
    {
        switch ($status) {
            case 1:
                return "administrator";
            default:
                return "user";
        }
    }
}
