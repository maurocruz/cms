<?php
namespace Plinct\Cms\Model\Api;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Tool\Curl\v1\Curl;

class WithCurl
{
	/**
	 * @var string|null
	 */
	private ?string $apiHost;
	/**
	 * @var Curl
	 */
	private Curl $curl;
	/**
	 * @var array|null
	 */
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
	 * @return WithCurl
	 */
	public function setUrl(string $url): WithCurl
	{
		$this->apiHost = $url;
		return $this;
	}

	/**
	 * @param ?string $relativeUrl
	 * @param array $params
	 * @return WithCurl
	 */
	public function get(string $relativeUrl = null, array $params = []): WithCurl
	{
		$this->curl->setUrl($this->apiHost.$relativeUrl)->get($params)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @param array|null $FILES
	 * @return WithCurl
	 */
	public function post(string $relativeUrl, array $data, array $FILES = NULL): WithCurl
	{
		$this->curl->setUrl($this->apiHost.$relativeUrl)->post($data, $FILES)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @return WithCurl
	 */
	public function put(string $relativeUrl, array $data): WithCurl
	{
		$this->curl->setUrl($this->apiHost.$relativeUrl)->put($data)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return WithCurl
	 */
	public function delete(string $relativeUrl, array $params): WithCurl
	{
		$this->curl->setUrl($this->apiHost.$relativeUrl)->delete($params)->returnWithJson();
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function ready(): mixed
	{
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
			CmsFactory::view()->Logger('apihost')->critical("$method: Api failed", ["url"=>$info['url'], "method"=>$method, "data"=>$this->data]);
			return ['status'=>'fail', 'message' => "Get api failed: url={$info['url']}; method=$method;"];
		} elseif (isset($returns['status'])) {
			if ($returns['status'] === 'fail') {
				CmsFactory::view()->Logger('apiHost')->critical("$method: Api failed", $returns);
				return ['status'=>'fail', 'message' => "Get api failed: {$returns['message']};", 'data'=>$returns['data'] ?? null];
			}
		}
		return $returns;
	}
}
