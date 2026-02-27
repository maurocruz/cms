<?php
namespace Plinct\Cms\Domain\Config;

class ConfigDomain
{
	private static string $apiHost;
	private static array $modulesAvailable = [];
	private static array $modulesEnabled = [];

	public static function setApiHost(string $apiHost): void
	{
		self::$apiHost = $apiHost;
	}

	/**
	 * @return string
	 */
	public static function getApiHost(): string
	{
		return self::$apiHost;
	}

	/**
	 * @param array $modulesAvailable
	 */
	public static function setModulesAvailable(array $modulesAvailable): void
	{
		self::$modulesAvailable = $modulesAvailable;
	}

	/**
	 * @return array
	 */
	public static function getModulesAvailable(): array
	{
		return self::$modulesAvailable;
	}

	/**
	 * @param array $modulesEnabled
	 */
	public static function setModulesEnabled(array $modulesEnabled): void
	{
		self::$modulesEnabled = $modulesEnabled;
	}

	/**
	 * @return array
	 */
	public static function getModulesEnabled(): array
	{
		return self::$modulesEnabled;
	}
}
