<?php
declare(strict_types=1);
namespace Plinct\Cms\Model\Authentication;

use Plinct\Cms\Controller\App;
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
		  $params = ['email'=>$email, 'password'=>$password ];
		  $responseData = CmsFactory::model()->api()->post("auth/login", $params)->ready();
		  $logger = CmsFactory::view()->Logger('auth', 'auth.log');
			if (is_string($responseData)) {
				$data = json_decode($responseData, true);
			  if (isset($data['status'])) {
				  if ($data['status'] === 'error') {
					  if (isset($data['data']['message'])) {
						  $logger->error("LOGIN ERROR from api host", $data['data']);
					  } else {
						  $logger->error("LOGIN ERROR in ". __FILE__." - ".__LINE__, $data);
					  }
				  }
				  if ($data['status'] === 'fail') {
					  $logger->info("LOGIN FAILED: " . $data['message'], ["email" => $email]);
				  }
				  if ($data['status'] === 'success') {
					  $logger->info("LOGIN SUCCESSFULLY: ".$data['message'], ["email" => $email]);
				  }
			  } else if ($data === null) {
					$logger->setChannel('error');
					$logger->critical('Get api failed', ['url'=>App::getApiHost()."auth/login", 'method'=>'post']);
			  }
			} else if (is_array($responseData)) {
				$data = $responseData;
				if (isset($responseData['status']) && $responseData['status'] === 'fail') {
					$logger->error("Get error from api", array_merge($responseData,  ['url'=>App::getApiHost()."auth/login", 'method'=>'post']));
				}
			} else {
				$data = false;
			}
		  // RETURN
	    return $data;
	  }
		return null;
  }

  /**
   * @param string $email
   * @return array
   */
  public function resetPassword(string $email): array
  {
    $params['email'] = $email;
    $params['mailHost'] = App::getMailHost();
    $params['mailUsername'] = App::getMailUsername();
    $params['mailPassword'] = App::getMailpassword();
    $params['urlToResetPassword'] = App::getUrlToResetPassword();
		return CmsFactory::model()->api()->post('auth/reset_password', $params)->ready();
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
