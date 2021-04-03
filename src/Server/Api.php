<?php
namespace Plinct\Cms\Server;

use Plinct\Api\Auth\AuthController;
use Plinct\Cms\App;
use Plinct\Tool\Curl;

class Api {

    public static function get(string $type, array $params = null) {
        return self::request($type, "get", $params);
    }

    public static function post(string $type, array $params) {
        return self::request($type, "post", $params);
    }

    public static function put(string $type, array $params) {
        return self::request($type, 'put', $params);
    }

    public static function delete(string $type, array $params) {
        return self::request($type, 'delete', $params);
    }

    public static function request($type, $action, $params) {
        if (App::getApiHost() == "localhost") {
            $classname = "Plinct\\Api\\Type\\".ucfirst($type);
            return (new $classname())->{$action}($params);
        } else {
            $token = filter_input(INPUT_COOKIE, "API_TOKEN");
            return json_decode((new Curl(App::getApiHost()))->{$action}($type, $params, $token), true);
        }
    }

    public static function login(string $email, string $password): ?array {
        if (App::getApiHost() == "localhost") {
            return (new AuthController())->login([ "email" => $email, "password" => $password ]);
        } elseif(filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
            return json_decode((new Curl(App::getApiHost()))->post("login", [ "email" => $email, "password" => $password ] ), true);
        }
        return null;
    }

    public static function register() {

    }
}