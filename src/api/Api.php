<?php
declare(strict_types=1);
namespace Plinct\Cms\api;

use Plinct\Cms\App;
use Plinct\Cms\logger\Logger;
use Plinct\Tool\ToolBox;

class Api
{
  /**
   * @param string $type
   * @param array|null $params
   * @return array
   */
  public function get(string $type, array $params = null): array
  {
    return self::request($type, "get", $params);
  }

  /**
   * @param string $type
   * @param array $params
   * @return array
   */
  public function post(string $type, array $params): array
  {
    return self::request($type, "post", $params);
  }

  /**
   * @param string $type
   * @param array $params
   * @return ?array
   */
  public function put(string $type, array $params): ?array
  {
    return self::request($type, 'put', $params);
  }

  /**
   * @param string $type
   * @param array $params
   * @return array
   */
  public function delete(string $type, array $params): array
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
	  $classname = "Plinct\\Api\\Type\\".ucfirst($type);
    if (App::getURL() == pathinfo($apiHostName)['dirname'] && class_exists($classname)) {
      return (new $classname())->{$action}($params);
    } else {
      // TOKEN
      $token = filter_input(INPUT_COOKIE, "API_TOKEN");
      // URL FOR API
	    $apiHostName = substr($apiHostName,-1) === '/' ? $apiHostName : $apiHostName.'/';
      $apiHostName = $apiHostName . $type . ($action == 'get' ?  "?" . http_build_query($params): null);
      // CURL
      $curlHandle = ToolBox::Curl()
        ->setUrl($apiHostName)
        ->$action($params)
	      ->returnWithJson();
      // METHOD
			if ($action !== 'get') $curlHandle->authorizationBear($token);

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
   * @param $params
   * @return false|mixed|string|null
   */
  public function register($params)
  {
		$data = null;
    unset($params['submit']);
    if (filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
			$curl = ToolBox::Curl(App::getApiHost().'auth/register');
			$dataCurl = $curl->post($params)->ready();
      $data = json_decode($dataCurl, true);
    }
	  // LOGGED
	  $logger = new Logger('auth', 'auth.log');
		if (isset($data['status'])) {
			if ($data['status'] === 'fail') {
				$logger->info("REGISTER FAILED: " . $data['message'], ['email' => $params['email']]);
			}
			if ($data['status'] === 'error') {
				$message = isset($data['data']['error']) ? $data['data']['error']['message'] : $data['message'];
				$logger->info("REGISTER ERROR: ".$message);
			}
			if ($data['status'] === 'success') {
				$logger->info("REGISTER SUCCESS: ".$data['message'], $data['data']);
			}
		} else if ($data === null) {
			$logger->critical("REGISTER FAILED: Api return is null");
		}
		// RETURN
    return $data;
  }

  /**
   * @param string $email
   * @return string
   */
  public function resetPassword(string $email): string
  {
    $url = App::getApiHost() . "auth/reset_password";

    $params['email'] = $email;
    $params['mailHost'] = App::getMailHost();
    $params['mailUsername'] = App::getMailUsername();
    $params['mailPassword'] = App::getMailpassword();
    $params['urlToResetPassword'] = App::getUrlToResetPassword();
		var_dump($url);
		var_dump($params);
    $handleCurl = ToolBox::Curl()->setUrl($url)->post($params)->returnWithJson();

    return $handleCurl->ready();
  }

  public function changePassword(array $params): string
  {
		unset($params['submit']);
		$base = substr(App::getApiHost(),-1) !== '/' ? App::getApiHost().'/' : App::getApiHost();
    $url = $base . "auth/change_password";
    $handleCurl = ToolBox::Curl()->setUrl($url)->post($params)->returnWithJson();
    // for localhost
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == "::1") $handleCurl->connectWithLocalhost();
    return $handleCurl->ready();
  }
}
