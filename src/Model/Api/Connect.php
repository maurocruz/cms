<?php
namespace Plinct\Cms\Model\Api;

use Exception;
use Plinct\Cms\Controller\App;

class Connect
{
	private string $method;
	private string $url;
	private array $params;
	private ?array $files;

	/**
	 * @param ?string $relativeUrl
	 * @return $this
	 */
	public function get(string $relativeUrl = null, array $params = []): Connect
	{
		$this->method = 'get';
		$this->url = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $data
	 * @param array|null $FILES
	 * @return $this
	 */
	public function post(string $relativeUrl, array $data, array $FILES = NULL): Connect
	{
		$this->method = 'post';
		$this->url = $relativeUrl;
		$this->params = $data;
		$this->files = $FILES;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function put(string $relativeUrl, array $params): Connect
	{
		$this->method = 'put';
		$this->url = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function delete(string $relativeUrl, array $params): Connect
	{
		$this->method = 'delete';
		$this->url = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @return array|string[]
	 * @throws Exception
	 */
	public function ready(): array
	{
		$urltoCurl = ['config','auth/login','auth/register','auth/reset_password','change_password','user/privileges','user'];

		if(App::isRemoteApi() || in_array($this->url, $urltoCurl)) {
			$curl = new WithCurl();
			if ($this->method == 'post') {
				$curl->post($this->url, $this->params, $this->files);
			} else {
				$curl->{$this->method}($this->url, $this->params);
			}
			return $curl->ready();
		} else {
			$connectBd = new WithDatabase();
			$connectBd->{$this->method}($this->url, $this->params);
			return $connectBd->ready();
		}
	}
}
