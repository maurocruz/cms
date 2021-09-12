<?php

declare(strict_types=1);

namespace Plinct\Cms\Server;

use Exception;
use Plinct\Api\Auth\AuthController;
use Plinct\Cms\App;
use Plinct\Tool\Curl;

class Api
{
    /**
     * @param string $type
     * @param array|null $params
     * @return array
     */
    public static function get(string $type, array $params = null): array
    {
        return self::request($type, "get", $params);
    }

    /**
     * @param string $type
     * @param array $params
     * @return array
     */
    public static function post(string $type, array $params): array
    {
        return self::request($type, "post", $params);
    }

    /**
     * @param string $type
     * @param array $params
     * @return array
     */
    public static function put(string $type, array $params): array
    {
        return self::request($type, 'put', $params);
    }

    /**
     * @param string $type
     * @param array $params
     * @return array
     */
    public static function delete(string $type, array $params): array
    {
        return self::request($type, 'delete', $params);
    }

    /**
     * @param $type
     * @param $action
     * @param $params
     * @return array
     */
    public static function request($type, $action, $params): array
    {
        $remoteAccessApi = null;

        // if api host equals cms app host
        if (App::$HOST == pathinfo(App::getApiHost())['dirname']) {
            $classname = "Plinct\\Api\\Type\\".ucfirst($type);
            $data = (new $classname())->{$action}($params);

            if (isset($data['error'])) {
                var_dump($data);
                die();
            }
            return $data;

        } else {
            $token = filter_input(INPUT_COOKIE, "API_TOKEN");

            try {
                $execRequest = (new Curl(App::getApiHost()))->{$action}($type, $params, $token);
                $remoteAccessApi = is_string($execRequest) ? json_decode((new Curl(App::getApiHost()))->{$action}($type, $params, $token), true) : ( $execRequest == true ? ['message'=>'true'] : ['message'=>'false']);
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

    /**
     * @param string $email
     * @param string $password
     * @return array|null
     */
    public static function login(string $email, string $password): ?array
    {
        if (App::$HOST == pathinfo(App::getApiHost())['dirname']) {
            return (new AuthController())->login([ "email" => $email, "password" => $password ]);

        } elseif (filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
            return json_decode((new Curl(App::getApiHost()))->post("login", [ "email" => $email, "password" => $password ]), true);
        }

        return null;
    }

    /**
     * @param $params
     * @return false|mixed|string|null
     */
    public static function register($params)
    {
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
