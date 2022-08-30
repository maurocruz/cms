<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\ToolBox;

class Auth
{
  /**
   * @param string $email
   * @param string $password
   * @return array|null
   */
  public function login(string $email, string $password): ?array
  {
	  if (filter_var(App::getApiHost(), FILTER_VALIDATE_URL)) {
			$apiHost = substr(App::getApiHost(),-1) !== "/" ? App::getApiHost() . "Auth.php/" : App::getApiHost();
		  $url = $apiHost."auth/login";
		  $params = ['email'=>$email, 'password'=>$password ];
			$curl = ToolBox::Curl()->setUrl($url)->post($params);
	    return json_decode($curl->ready(), true);
	  }
		return null;
  }

  /**
   * @param $params
   * @return false|mixed|string|null
   */
  public function register($params) {
    unset($params['submit']);
    return CmsFactory::request()->server()->api()->post('auth/register',$params)->ready();
  }

  /**
   * @param string $email
   * @return array
   */
  public function resetPassword(string $email): array
  {
    $url = App::getApiHost() . "auth/reset_password";

    $params['email'] = $email;
    $params['mailHost'] = App::getMailHost();
    $params['mailUsername'] = App::getMailUsername();
    $params['mailPassword'] = App::getMailpassword();
    $params['urlToResetPassword'] = App::getUrlToResetPassword();

    $handleCurl = ToolBox::Curl()->setUrl($url)->post($params)->returnWithJson();

    return json_decode($handleCurl->ready(), true);
  }

	/**
	 * @param array $params
	 * @return string
	 */
  public function changePassword(array $params): string
  {
		unset($params['submit']);
		$base = substr(App::getApiHost(),-1) !== '/' ? App::getApiHost() . 'Auth.php/' : App::getApiHost();
    $url = $base . "auth/change_password";
    $handleCurl = ToolBox::Curl()->setUrl($url)->post($params)->returnWithJson();
    // for localhost
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == "::1") $handleCurl->connectWithLocalhost();

    return json_decode($handleCurl->ready(), true);
  }
}
