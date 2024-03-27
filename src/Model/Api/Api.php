<?php
declare(strict_types=1);
namespace Plinct\Cms\Model\Api;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Tool\Curl\v1\Curl;

class Api
{
	/**
	 * @var string|null
	 */
	private string $apiHost;
	/**
	 * @var Curl
	 */
	private Curl $curl;

	private ?array $data = null;

	/**
	 * @param string|null $apiHost
	 */
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
	public function post(string $relativeUrl, array $data, array $FILES = NULL): Api {
		$this->data = $data;
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
		// ready curl
		$data = $this->curl->ready();
		// info
		$info = $this->curl->getInfo();
		$method = $info['effective_method'] ?? null;
		$returns = json_decode($data, true);
		if ($returns === null) {
			CmsFactory::view()->Logger('apihost')->critical("$method: Api failed (api.php 97)", ["url"=>$info['url'], "method"=>$method, "data"=>$this->data]);
			return ['status'=>'fail', 'message' => "Get api failed: api.php 99"];
		} elseif (isset($returns['status'])) {
			if ($returns['status'] === 'fail') {
				CmsFactory::view()->Logger('apiHost')->critical("$method: Api failed", $returns);
			}
		}
		return $returns;
	}
}
