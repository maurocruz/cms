<?php
namespace Plinct\Cms\Application\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Support\SupportApplication;
use Plinct\Cms\Infrastructure\Modules\ModuleApiProvider;

readonly class ActionUseCase
{
	public function __construct(private ModuleApiProvider $moduleApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function withObject(array $queryParams): array
	{
		$object = $queryParams['object'] ?? null;
		// OBJECT
		if ($object) {
			$dataApi = $this->moduleApi->read('thing',['idthing'=>$object, 'hasPart'=>true]);
			$dataUseCase = SupportApplication::returnsModules('Thing', $dataApi);
			return $dataUseCase['status'] === true ? ['object' => $dataUseCase['data'][0] ?? []] : [];
		}
		return ['status'=>true, 'message'=>'No object found', 'data'=>[]];
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(string $id, array $queryParams = []): array
	{
		$params = array_merge(['idaction' => $id, 'properties' => 'object'], $queryParams);
		$dataApi = $this->moduleApi->read('action', $params);
		return SupportApplication::returnsModules('Action', $dataApi);
	}

}
