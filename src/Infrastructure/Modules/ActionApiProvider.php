<?php
namespace Plinct\Cms\Infrastructure\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Http\ApiClient;

readonly class ActionApiProvider
{
	public function __construct(private ApiClient $client)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(array $params = [])
	{
		$this->client->setQueries($params);
		return $this->client->get('action');
	}
}
