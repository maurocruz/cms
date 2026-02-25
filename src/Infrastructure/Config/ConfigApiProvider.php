<?php
namespace Plinct\Cms\Infrastructure\Config;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Http\ApiClient;

readonly class ConfigApiProvider
{
	public function __construct(private ApiClient $client)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function installModule(string $module): array
	{
		$this->client->setQueries([]);
		return $this->client->post('config/installModule', ['module' => $module]);
	}
}
