<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class UserController implements ControllerInterface
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        $params = [ "format" => "ItemList" ];
        return Api::get("user", $params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params = array_merge($params, [ "properties" => "email,create_time" ]);
        $data = Api::get("user", $params);
        return $data[0];        
    }

    /**
     * @param null $params
     * @return null
     */
    public function new($params = null)
    {
        return null;
    }

    /**
     * @param $status
     * @return string
     */
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
