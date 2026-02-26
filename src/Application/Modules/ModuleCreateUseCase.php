<?php
namespace Plinct\Cms\Application\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Modules\ModuleApiProvider;

readonly class ModuleCreateUseCase
{
	public function __construct(private ModuleApiProvider $moduleApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function create(string $type, array $data): array
	{
		$dataApi = $this->moduleApi->create($type, $data);
		if (isset($dataApi['status']) && $dataApi['status'] === 'success') {
			return ['status'=>true, 'message'=>'Module created successfully', 'data'=>$dataApi['data'][0]];
		} else {
			return ['status'=>false, 'message'=>'Error', 'data'=>$dataApi['message']];
		}
	}
}
