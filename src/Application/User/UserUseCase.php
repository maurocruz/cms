<?php
namespace Plinct\Cms\Application\User;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Abstracts\UseCaseAbstract;
use Plinct\Cms\Infrastructure\User\ApiUserProvider;

class UserUseCase extends UseCaseAbstract
{

	public function __construct(private readonly ApiUserProvider $client)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function list(array $queryParams = []): array
	{
		// GET API DATA (Infrastructure)
		$data = $this->client->list($queryParams);
		// RETURN
		return ['status'=>true, 'data'=>$data, 'queryParams'=>$queryParams];
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(string $id): array
	{
		$data = $this->client->show($id);
		if (isset($data[0])) {
			return ['status'=>true, 'data'=>$data[0]];
		}
		return ['status'=>false, 'message'=>'User not found', 'data'=>$data];
	}

}
