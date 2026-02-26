<?php
namespace Plinct\Cms\Domain\Config;

class ConfigDomain
{
	private static string $apiHost;

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

}
