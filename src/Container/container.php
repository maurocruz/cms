<?php

use GuzzleHttp\Client;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Cms\Application\Auth\AuthenticatedUser;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Http\View\Fragment\FragmentFactory;
use Plinct\Cms\Http\View\Template\Template;
use Plinct\Cms\Http\View\ViewFactory;
use Plinct\Cms\Infrastructure\Auth\ApiPrivilegeProvider;
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

	ApiPrivilegeProvider::class => DI\autowire(ApiPrivilegeProvider::class),

	AuthenticatedUser::class => DI\autowire(AuthenticatedUser::class),

	ViewFactory::class => DI\autowire(ViewFactory::class),

	Template::class => DI\autowire(Template::class),

	\Plinct\Tool\Locale::class => DI\autowire(\Plinct\Tool\Locale::class),

	FragmentFactory::class => DI\autowire(FragmentFactory::class),

	RequestContext::class => DI\autowire(RequestContext::class),

];