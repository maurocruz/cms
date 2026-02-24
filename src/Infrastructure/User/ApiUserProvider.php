<?php
namespace Plinct\Cms\Infrastructure\User;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Http\ApiClient;

readonly class ApiUserProvider
{
	public function __construct(private ApiClient $client)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function list($queryParams = [])
	{
		$this->client->setQueries($queryParams);
		return $this->client->get('user');
	}

	/**
	 * @throws GuzzleException
	 */
	public function show($id)
	{
		$this->client->setQueries(['id'=>$id, 'properties'=>'privileges, userCreator']);
		return $this->client->get('user');
	}
}
