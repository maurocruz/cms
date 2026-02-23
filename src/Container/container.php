<?php

use GuzzleHttp\Client;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Cms\Application\Authentication\AuthenticatedUser;
use Plinct\Cms\Application\Context\GetRepo;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Domain\Cache\CacheInterface;
use Plinct\Cms\Infrastructure\Auth\JwtDecoderHttp;
use Plinct\Cms\Http\ExceptionHandlers\HttpForbiddenHandler;
use Plinct\Cms\Http\ExceptionHandlers\HttpNotFoundHandler;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Template\Template;
use Plinct\Cms\Infrastructure\Cache\FileCache;
use Plinct\Cms\Infrastructure\Http\ApiClient;
use Psr\Container\ContainerInterface;

return [

	ApiFactory::class => DI\autowire(ApiFactory::class),

	PDOConnect::class => DI\autowire(PDOConnect::class),

	Client::class => DI\factory(function (ContainerInterface $container) {
		return new Client([
			'timeout' => 10.0,
			'http_errors' => false,
			'base_uri' => $container->get('settings')['apiHost'],
			'verify' => !$container->get('settings')['debug']
		]);
	}),

	ApiClient::class => DI\autowire(ApiClient::class),

	AuthenticatedUser::class => DI\autowire(AuthenticatedUser::class),

	Template::class => DI\autowire(Template::class),

	\Plinct\Tool\Locale::class => DI\autowire(\Plinct\Tool\Locale::class),

	ComponentFactory::class => DI\autowire(ComponentFactory::class),

	RequestContext::class => DI\autowire(RequestContext::class),

	JwtDecoderHttp::class => DI\autowire(JwtDecoderHttp::class),

	HttpNotFoundHandler::class => DI\autowire(HttpNotFoundHandler::class),

	HttpForbiddenHandler::class => DI\autowire(HttpForbiddenHandler::class),

	CacheInterface::class => DI\factory(function (ContainerInterface $container) {
		$settings = $container->get('settings');
		$basedir = isset($settings['basedir']) && is_string($settings['basedir']) ? $settings['basedir'] : null;

		$dir = $basedir ? ($basedir . '/var/cache') : null;
		return new FileCache($dir);
	}),

	GetRepo::class => DI\autowire(GetRepo::class),
];