<?php
namespace Plinct\Cms\Infrastructure\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ApiClient
{
	private $client;
	private string $apiHost;
	private array $options = [];
	private string $uri = '';
	private ?string $url = null;
	private $response;
	private string $method;

	/**
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __construct(Client $client, ContainerInterface $container)
	{
		$this->client = $client;
		$this->apiHost = $container->get('settings')['apiHost'];
	}

	/**
	 * @param string $user_token
	 */
	public function setUserToken(string $user_token): void
	{
		$this->options['headers']['Authorization'] = 'Bearer '.$user_token;
		$this->options['headers']['Accept'] = 'application/json';
	}

	/**
	 * @param array $queries
	 */
	public function setQueries(array $queries): void
	{
		$this->options['query'] = $queries;
	}

	public function setFormParams(array $params): void
	{
		$this->options['form_params'] = $params;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param string $uri
	 */
	public function setUri(string $uri): void
	{
		$this->uri = $uri;
	}

	/**
	 * @throws GuzzleException
	 */
	public function get(string $uri = null)
	{
		$this->method = 'GET';
		// URL
		$this->url = $this->url ?? $uri ? $this->apiHost.$uri : $this->apiHost.$this->uri;
		// REQUEST
		$this->response = $this->client->get($this->url, $this->options);
		return $this->render();
	}

	/**
	 * @throws GuzzleException
	 */
	public function post(string $uri = null, array $formParams = null)
	{
		$this->method = 'POST';
		// URL
		$this->url = $this->url ?? $uri ? $this->apiHost.$uri : $this->apiHost.$this->uri;
		// FORM PARAMS
		if ($formParams !== null) {
			$this->options['form_params'] = isset($this->options['form_params']) ? array_merge($this->options['form_params'], $formParams) : $formParams;
		}
		// REQUEST
		$this->response = $this->client->post($this->url, $this->options);
		return $this->render();
	}

	/**
	 * @throws GuzzleException
	 */
	public function put(string $uri, array $formParams = null)
	{
		$this->method = 'PUT';
		// URL
		$this->url = $this->url ?? $uri ? $this->apiHost.$uri : $this->apiHost.$this->uri;
		// FORM PARAMS
		if ($formParams !== null) {
			$this->options['form_params'] = isset($this->options['form_params']) ? array_merge($this->options['form_params'], $formParams) : $formParams;
		}
		// REQUEST
		$this->response = $this->client->put($this->url, $this->options);
		return $this->render();
	}

	/**
	 * @return array|mixed
	 */
	private function render(): mixed
	{
		if ($this->response->getStatusCode() == 200) {
			return json_decode($this->response->getBody()->getContents(), true);
		} else {
			return ["status" => $this->response->getStatusCode(), "reasonPhrase" => $this->response->getReasonPhrase(), "method"=>$this->method, "url"=>$this->url, "options" => $this->options];
		}
	}


}
