<?php
namespace Plinct\Cms\Application\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Abstracts\UseCaseAbstract;
use Plinct\Cms\Infrastructure\Modules\ModuleApiProvider;

class CreativeWorkUseCase extends UseCaseAbstract
{
	public function __construct(private readonly ModuleApiProvider $moduleApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function findItem(string $id): array
	{
		$dataApi = $this->moduleApi->read('creativeWork', ['idcreativeWork' => $id]);
		return $this->returnData('CreativeWork', $dataApi);
	}
}
