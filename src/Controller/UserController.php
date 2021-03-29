<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class UserController implements ControllerInterface
{   
    public function index($params = null): array {
        $params = [ "format" => "ItemList" ];
        return Api::get("user", $params);
    }
    
    public function edit($params): array {
        $params = array_merge($params, [ "properties" => "email,create_time" ]);
        $data = Api::get("user", $params);
        return $data[0];        
    }
    
    public function new($params = null) {
        return null;
    }

    public static function getStatusWithText($status): string {
        switch ($status) {
            case 1:
                return "administrator";
            default:
                return "user";
        }
    }

    public function authentication($params): array {
        $data = Api::login($params['email'], $params['password']);
        return (array) $data;
    }
}
