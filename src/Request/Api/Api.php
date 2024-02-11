<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Request\Api;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\Controller\logger\Logger;
use Plinct\Tool\Curl\v1\Curl;

class Api
{
	private string $apiHost;
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
		$token = CmsFactory::request()->user()->userLogged()->getToken();
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
}
