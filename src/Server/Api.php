<?php

declare(strict_types=1);

namespace Plinct\Cms\Server;

use Plinct\Api\Auth\AuthController;
use Plinct\Cms\App;
use Plinct\Tool\Curl;
use Plinct\Tool\ToolBox;

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
     * @return ?array
     */
    public static function put(string $type, array $params): ?array
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
     * @return ?array
     */
    public static function request($type, $action, $params): ?array
    {
        $apiHostName = App::getApiHost();

        // IF SITE HOST === API HOST
        if (App::$HOST == pathinfo($apiHostName)['dirname']) {
            $classname = "Plinct\\Api\\Type\\".ucfirst($type);
            return (new $classname())->{$action}($params);

        } else {
            // TOKEN
            $token = filter_input(INPUT_COOKIE, "API_TOKEN");

            // URL FOR API
            $apiHostName = $apiHostName . $type . ($action == 'get' ?  "?" . http_build_query($params): null);

            // CURL
            $curlHandle = ToolBox::Curl()
                ->setUrl($apiHostName)
                ->returnWithJson();

            // METHOD
            if ($action !== 'get') $curlHandle->method($action)->authorizationBear($token)->params($params);

            // LOCALHOST
            if (substr($curlHandle->getInfo()['primary_ip'],0,3) == '127') {
               $curlHandle->connectWithLocalhost();
            }

            // READY
            $ready = $curlHandle->ready();

            // JSON
            $json = json_decode($ready, true);

            // RETURN IF ERROR
            if (json_last_error() === 0) {
                return $json;
            } else {
                return [ "error" => [
                    "message" => $ready
                ]];
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
