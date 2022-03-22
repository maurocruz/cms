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
    if (App::getURL() == pathinfo($apiHostName)['dirname']) {
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
        ->method($action);
      // METHOD
			if ($action == 'put') {
				$curlHandle->authorizationBear($token)->put($params);
			} elseif ($action !== 'get') {
				$curlHandle->authorizationBear($token)->params($params);
			} else {
				$curlHandle->returnWithJson();
			}
      // LOCALHOST
      $ipAddress = substr($curlHandle->getInfo()['local_ip'],0,3);
      if ( $ipAddress <= 127 || ($ipAddress >= 192 && $ipAddress <= 233 )) {
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
          return [
              "status" => "error",
              "message" => $ready
          ];
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
        if (filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
            $curl = ToolBox::Curl()->setUrl(App::getApiHost()."/auth/login")->method('post')->params([ "email" => $email, "password" => $password ])->returnWithJson();
            return json_decode($curl->ready(), true);
        } else {
            return (new AuthController())->login([ "email" => $email, "password" => $password ]);
        }
    }

    /**
     * @param $params
     * @return false|mixed|string|null
     */
    public static function register($params)
    {
        unset($params['passwordRepeat']);
        unset($params['submit']);

        if (App::getURL() == pathinfo(App::getApiHost())['dirname']) {
            return (new AuthController())->register($params);

        } elseif(filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
            return json_decode((new Curl(App::getApiHost()))->post("register", $params), true);
        }

        return null;
    }

    /**
     * @param string $email
     * @return string
     */
    public static function resetPassword(string $email): string
    {
        $url = App::getApiHost() . "auth/reset_password";

        $params['email'] = $email;
        $params['mailHost'] = App::getMailHost();
        $params['mailUsername'] = App::getMailUsername();
        $params['mailPassword'] = App::getMailpassword();
        $params['urlToResetPassword'] = App::getUrlToResetPassword();

        $handleCurl = ToolBox::Curl()->setUrl($url)->method('post')->params($params)->returnWithJson();

        return $handleCurl->ready();
    }

    public static function changePassword(array $params): string
    {
        $url = App::getApiHost() . "auth/change_password";

        $handleCurl = ToolBox::Curl()->setUrl($url)->method('post')->params($params)->returnWithJson();

        // for localhost
        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == "::1") $handleCurl->connectWithLocalhost();

        return $handleCurl->ready();
    }
}
