<?php
namespace Plinct\Cms\Application\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Support\SupportApplication;
use Plinct\Cms\Infrastructure\Modules\ModuleApiProvider;

readonly class ModuleUseCase
{
	public function __construct(private ModuleApiProvider $moduleApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(string $type, array $params = [] ): array
	{
		$dataApi = $this->moduleApi->read($type, $params);
		return SupportApplication::returnsModules($dataApi);
	}
}
