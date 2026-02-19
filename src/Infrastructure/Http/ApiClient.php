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

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @throws GuzzleException
	 */
	public function get(string $uri = null)
	{
		$response = $this->client->get($this->apiHost.$uri, $this->options);
		return json_decode($response->getBody()->getContents(), true);
	}
}
