<?php
namespace Plinct\Cms\Infrastructure\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Http\ApiClient;

class ApiPrivilegeProvider
{
	private ApiClient $client;

	public function __construct(ApiClient $client)
	{
		$this->client = $client;
	}

	/**
	 * @throws GuzzleException
	 */
	public function getPrivileges(int $iduser, string $token): array
	{
		$this->client->setQueries(['iduser'=>$iduser,'properties'=>'privileges']);
		$this->client->setUserToken($token);
		return $this->client->get('user');
	}
}
