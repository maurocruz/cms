<?php
namespace Plinct\Cms\Infrastructure\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Http\ApiClient;

readonly class ModuleApiProvider
{
	public function __construct(private ApiClient $client)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function read(string $moduleName, $params = []) {
		$this->client->setQueries($params);
		return $this->client->get($moduleName);
	}

	/**
	 * @throws GuzzleException
	 */
	public function create(string $moduleName, $params = [], $filesUpload = [])
	{
		return $this->client->post($moduleName, $params);
	}

}
