<?php
namespace Plinct\Cms\Http\Middleware;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Domain\Cache\CacheInterface;
use Plinct\Cms\Domain\Config\ConfigDomain;
use Plinct\Cms\Infrastructure\Http\ApiClient;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


readonly class RemoteProcedureCallMiddleware implements MiddlewareInterface
{
	private const string RPC_ATTRIBUTE = 'RPC';
	private const string TYPE_SERVICE = 'Service';
	private const string OFFER_IN_STOCK = 'InStock';

	private const int CACHE_TTL_CONFIG_OK_SECONDS = 86400;     // 24 h
	private const int CACHE_TTL_HOME_SECONDS = 10;     // 5 s

	private ApiClient $api;
	private string $apiHost;
	private CacheInterface $cache;

	public function __construct(ApiClient $api, CacheInterface $cache, ContainerInterface $container)
	{
		$this->api = $api;
		$this->cache = $cache;
		$settings = [];
		try {
			$settings = $container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}
		if (!is_array($settings)) {
			$settings = [];
		}
		$this->apiHost = isset($settings['apiHost']) && is_string($settings['apiHost']) ? $settings['apiHost'] : '';
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$schemaOk = false;
		$tablesOk = false;
		$modulesAvailable = [];
		$modulesEnabled = [];

		try {
			// CAPTURA ESTADOS DOS MODULOS NA API (MODULES AVAILABLE AND ENABLED)
			$cacheKey = $this->cache->setKey('RPC','api_home',['apihost'=>$this->apiHost]);
			if ($this->cache->has($cacheKey)) {
				$apiHome = $this->cache->get($cacheKey);
			} else {
				$apiHome = $this->api->get();
				$this->cache->set($cacheKey, $apiHome, self::CACHE_TTL_HOME_SECONDS);
			}
			if (is_array($apiHome) && !isset($apiHome['error'])) {
				$schemaOk = true;
				$homeGraph = $apiHome['@graph'] ?? null;
				if (is_array($homeGraph)) {
					foreach ($homeGraph as $value) {
						if (!is_array($value)) {
							continue;
						}
						if (($value['@type'] ?? null) !== self::TYPE_SERVICE) {
							continue;
						}
						$name = $value['name'] ?? null;
						if (!is_string($name) || $name === '') {
							continue;
						}
						$offers = $value['offers'] ?? null;
						$modulesAvailable[] = $name;
						if ($offers === self::OFFER_IN_STOCK) {
							$modulesEnabled[] = $name;
						}
					}
				}

				// CAPTURA CONFIGURACAO DA BASE DE DADOS
				if ($this->apiHost !== '') {
					$apiConfigKey = $this->cache->setKey("rpc","api_config",["url"=>"$this->apiHost/config"]);
					$ttl = self::CACHE_TTL_CONFIG_OK_SECONDS;
					if ($this->cache->has($apiConfigKey)) {
						$apiConfigData = $this->cache->get($apiConfigKey);
					} else {
						$apiConfigData = $this->api->get('config');
						$this->cache->set($apiConfigKey, $apiConfigData, $ttl);
					}
					$configGraph = is_array($apiConfigData) ? ($apiConfigData['@graph'] ?? null) : null;
					if (is_array($configGraph)) {
						$graphIndexes = array_column($configGraph, null, '@id');
						$databaseId = $this->apiHost . "config#database";
						$database = $graphIndexes[$databaseId] ?? null;
						$size = is_array($database) ? ($database['size'] ?? null) : null;
						$tablesOk = is_numeric($size) && (int)$size > 0;
					}
				}
			}
		} catch (GuzzleException $e) {
			error_log($e->getMessage());
		}

		$RPC_Attr = [
			'apiHostName' => $this->apiHost,
			'schema' => $schemaOk,
			'tables' => $tablesOk,
		];
		ConfigDomain::setModulesAvailable($modulesAvailable);
		ConfigDomain::setModulesEnabled($modulesEnabled);
		$request = $request->withAttribute('MODULES_AVAILABLE', $modulesAvailable);
		$request = $request->withAttribute('MODULES_ENABLED', $modulesEnabled);
		$request = $request->withAttribute(self::RPC_ATTRIBUTE, $RPC_Attr);

		return $handler->handle($request);
	}
}
