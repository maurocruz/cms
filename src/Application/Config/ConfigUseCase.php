<?php
namespace Plinct\Cms\Application\Config;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Infrastructure\Config\ConfigApiProvider;

readonly class ConfigUseCase
{
	public function __construct(private ConfigApiProvider $configApi)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function installModule(string $moduleName): array
	{
		return $this->configApi->installModule($moduleName);
	}
}
