<?php
namespace Plinct\Cms\Server;

use Exception;
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
        if (App::$HOST == pathinfo(App::getApiHost())['dirname']) {
            $classname = "Plinct\\Api\\Type\\".ucfirst($type);
            return (new $classname())->{$action}($params);
        } else {
            $token = filter_input(INPUT_COOKIE, "API_TOKEN");
            try {
                $remoteAccessApi = json_decode((new Curl(App::getApiHost()))->{$action}($type, $params, $token), true);
                if (isset($remoteAccessApi['error'])) {
                    throw new Exception($remoteAccessApi['error']['message']);
                } else {
                    return $remoteAccessApi;
                }
            } catch (Exception $e) {
                var_dump($remoteAccessApi['error']);
                die;
            }
        }
    }

    public static function login(string $email, string $password): ?array {
        if (App::$HOST == pathinfo(App::getApiHost())['dirname']) {
            return (new AuthController())->login([ "email" => $email, "password" => $password ]);
        } elseif(filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
            return json_decode((new Curl(App::getApiHost()))->post("login", [ "email" => $email, "password" => $password ]), true);
        }
        return null;
    }

    public static function register($params) {
        unset($params['passwordRepeat']);
        unset($params['submit']);
        if (App::$HOST == pathinfo(App::getApiHost())['dirname']) {
            return (new AuthController())->register($params);
        } elseif(filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
            return json_decode((new Curl(App::getApiHost()))->post("register", $params), true);
        }
        return null;
    }
}