<?php
namespace Plinct\Cms\Model\Api;

use Exception;
use Plinct\Api\ApiFactory;

class WithDatabase
{
	/**
	 * @var string
	 */
	private string $relativeUrl;
	/**
	 * @var array
	 */
	private array $params = [];
	/**
	 * @var string
	 */
	private string $method;

	/**
	 * @param string|null $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function get(string $relativeUrl = null, array $params = []): static
	{
		$this->method = 'GET';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function post(string $relativeUrl, array $params): static
	{
		$this->method = 'POST';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function put(string $relativeUrl, array $params): static
	{
		$this->method = 'PUT';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @param string $relativeUrl
	 * @param array $params
	 * @return $this
	 */
	public function delete(string $relativeUrl, array $params): static
	{
		$this->method = 'DELETE';
		$this->relativeUrl = $relativeUrl;
		$this->params = $params;
		return $this;
	}

	/**
	 * @throws Exception
	 */
	public function ready(): array
	{
		// CONFIG
		$data = ApiFactory::request()->type($this->relativeUrl)->{$this->method}($this->params)->ready();
		return ApiFactory::response()->type($this->relativeUrl)->setData($data)->setParams($this->params)->ready();
	}
}
