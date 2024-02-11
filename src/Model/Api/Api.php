<?php
declare(strict_types=1);
namespace Plinct\Cms\Model\Api;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Cms\View\logger\Logger;
use Plinct\Tool\Curl\v1\Curl;
use Plinct\Tool\ToolBox;

class Api
{private string $apiHost;
	private Curl $curl;

	public function __construct(string $apiHost = null)	{
		$this->apiHost = $apiHost ?? App::getApiHost();
		$this->curl = new Curl();
	}

	/**
	 * @param string $url
	 * @return Api
	 */
	public function setUrl(string $url): Api
	{
		$this->apiHost = $url;
		return $this;
	}

	/**
	 * @param ?string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function get(string $relativeUrl = null, array $params = []): Api
	{
		$this->curl->setUrl($this->apiHost.$relativeUrl)->get($params)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @param array|null $FILES
	 * @return $this
	 */
	public function post(string $relativeUrl, array $data, array $FILES = NULL): Api
	{
		$this->curl->setUrl($this->apiHost.$relativeUrl)->post($data, $FILES)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @return $this
	 */
	public function put(string $relativeUrl, array $data): Api {
		$this->curl->setUrl($this->apiHost.$relativeUrl)->put($data)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function delete(string $relativeUrl, array $params): Api {
		$this->curl->setUrl($this->apiHost.$relativeUrl)->delete($params)->returnWithJson();
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function ready() {
		$token = CmsFactory::controller()->user()->userLogged()->getToken();
		if($token) {
			$this->curl->authorizationBear($token);
		}
		$info = $this->curl->getInfo();
		$data = $this->curl->ready();
		$returns = json_decode($data, true);
		if ($returns === null) {
			(new Logger('apihost','error.log'))->critical("Get api failed", ["url"=>$info['url']]);
			return ['status'=>'fail', 'message' => 'User not authorized for this operation'];
		}
		return $returns;
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
		$base = substr(App::getApiHost(),-1) !== '/' ? App::getApiHost() . 'Api.php/' : App::getApiHost();
    $url = $base . "auth/change_password";
    $handleCurl = ToolBox::Curl()->setUrl($url)->post($params)->returnWithJson();
    // for localhost
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == "::1") $handleCurl->connectWithLocalhost();
    return $handleCurl->ready();
  }
}
