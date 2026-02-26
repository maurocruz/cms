<?php
namespace Plinct\Cms\Application\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Modules\ActionApiProvider;

readonly class ActionUseCase
{
	public function __construct(private ActionApiProvider $actionApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(string $id, array $queryParams = []): array
	{
		$params = array_merge(['idaction' => $id], $queryParams);
		$dataApi = $this->actionApi->show($params);
		if (isset($dataApi[0])) {
			return ['status'=>true, 'data'=>$dataApi[0]];
		} elseif (empty($dataApi)) {
			return ['status' => false, 'message' => 'Action not found', 'data' => $dataApi];
		} else {
			return ['status' => false, 'message' => 'Error', 'data' => $dataApi];
		}
	}
}
