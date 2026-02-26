<?php

use GuzzleHttp\Client;
use Plinct\Cms\Domain\Cache\CacheInterface;
use Plinct\Cms\Infrastructure\Cache\FileCache;
use Psr\Container\ContainerInterface;

return [
	Client::class => DI\factory(function (ContainerInterface $container) {
		return new Client([
			'timeout' => 10.0,
			'http_errors' => false,
			'base_uri' => $container->get('settings')['apiHost'],
			'verify' => !$container->get('settings')['debug']
		]);
	}),

	CacheInterface::class => DI\factory(function (ContainerInterface $container) {
		$settings = $container->get('settings');
		$basedir = isset($settings['basedir']) && is_string($settings['basedir']) ? $settings['basedir'] : null;

		$dir = $basedir ? ($basedir . '/var/cache') : null;
		return new FileCache($dir);
	})
];
