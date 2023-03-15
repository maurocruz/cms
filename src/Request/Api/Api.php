<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Api;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\Curl\v1\Curl;

class Api
{
	private string $hostApi;
	private Curl $curl;

	public function __construct(string $hostApi = null)
	{
		$this->hostApi = $hostApi ?? App::getApiHost();
		$this->curl = new Curl();
	}

	/**
	 * @param string $relativeUrl
	 * @param ?array $params
	 * @return $this
	 */
	public function get(string $relativeUrl, array $params = []): Api
	{
		$this->curl->setUrl($this->hostApi.$relativeUrl)->get($params)->returnWithJson();
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
		$this->curl->setUrl($this->hostApi.$relativeUrl)->post($data, $FILES)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @return $this
	 */
	public function put(string $relativeUrl, array $data): Api {
		$this->curl->setUrl($this->hostApi.$relativeUrl)->put($data)->returnWithJson();
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function delete(string $relativeUrl, array $params): Api {
		$this->curl->setUrl($this->hostApi.$relativeUrl)->delete($params)->returnWithJson();
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

		$returns = json_decode($this->curl->ready(), true);

		if ($returns === null) {
			return ['status'=>'fail', 'message' => 'User not authorized for this operation'];
		}

		return $returns;
	}
}
