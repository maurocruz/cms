<?php

namespace Plinct\Cms\Application\Modules;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Support\SupportApplication;
use Plinct\Cms\Infrastructure\Modules\ModuleApiProvider;

readonly class ModuleUpdateUseCase
{
	public function __construct(private ModuleApiProvider $moduleApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function update(string $type, array $data): array
	{
		$dataApi = $this->moduleApi->update($type, $data);
		return SupportApplication::returnsActions('update', $dataApi);
	}
}
