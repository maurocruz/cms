<?php
namespace Plinct\Cms\Http\Middleware;

use Exception;
use Gitonomy\Git\Repository;
use GuzzleHttp\Exception\GuzzleException;
use Locale;
use Plinct\Cms\Application\Context\RequestContext;
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
	private const RPC_ATTRIBUTE = 'RPC';
	private const CONFIG_URI = 'config';
	private const TYPE_SERVICE = 'Service';
	private const OFFER_OUT_OF_STOCK = 'OutOfStock';
	private const OFFER_IN_STOCK = 'InStock';

	private ApiClient $api;
	private string $apiHost;
	private string $sitename;
	private string $basedir;

	public function __construct(ApiClient $api, ContainerInterface $container)
	{
		$this->api = $api;
		$settings = '';
		try {
			$settings = $container->get('settings');
		} catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}
		// APIHOST
		$this->apiHost = isset($settings['apiHost']) && is_string($settings['apiHost']) ? $settings['apiHost'] : '';
		// BASEDIR
		$this->basedir = isset($settings['basedir']) && is_string($settings['basedir']) ? $settings['basedir'] : '';
		// SITENAME
		$this->sitename = isset($settings['sitename']) && is_string($settings['sitename']) ? $settings['sitename'] : '';
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$schemaOk = false;
		$tablesOk = false;
		$context = new RequestContext();
		$uri = $request->getUri();

		try {
			$apiHome = $this->api->get();

			if (is_array($apiHome) && !isset($apiHome['error'])) {
				$schemaOk = true;
				$modulesAvailable = [];
				$modulesEnabled = [];

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
						if ($offers === self::OFFER_OUT_OF_STOCK) {
							$modulesAvailable[] = $name;
						} elseif ($offers === self::OFFER_IN_STOCK) {
							$modulesEnabled[] = $name;
						}
					}
				}

				$repo = self::getRepo();
				$version = $repo['version'] ?? '';
				$commit = $repo['commit'] ?? '';

				$context->setModules($modulesAvailable, $modulesEnabled);
				$context->setHost($uri->getScheme().'://'.$uri->getHost());
				$context->setApiHost($this->apiHost);
				$context->setSitename($this->sitename);
				$context->setVersion($version);
				$context->setCommit($commit);
				$context->setBasedirectory($this->basedir);
				if (filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE')) {
					$context->setLocale((new Locale())->acceptFromHttp(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE')));
				}
				$request = $request->withAttribute(RequestContext::class, $context);

				if ($this->apiHost !== '') {
					$apiConfig = $this->api->get(self::CONFIG_URI);

					$configGraph = $apiConfig['@graph'] ?? null;
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
		$request = $request->withAttribute(self::RPC_ATTRIBUTE, $RPC_Attr);

		return $handler->handle($request);
	}

	/**
	 * @return array
	 */
	private function getRepo(): array
	{
		$version = 'NAN';
		$commit = null;
		$gitDirectory = realpath($this->basedir . '/.git');
		if ($gitDirectory && class_exists(Repository::class)) {
			$repository = new Repository($gitDirectory);
			$head = $repository->getHead();
			$revision = rtrim(preg_replace("/(.*?\/){2}/", '', $head->getRevision()));
			$commitHash = $head->getCommitHash();
			$references = $repository->getReferences();
			$tags = $references->resolveTags($commitHash);
			if (!empty($tags)) {
				$commit = rtrim(preg_replace("/(.*?\/){2}/", '', $tags[0]->getFullname()));
			} else {
				$commit = substr($commitHash,0,8);
			}
			$version = $revision;

		} else {
			$installedFile = realpath($_SERVER['DOCUMENT_ROOT'] . "/../vendor/composer/installed.json");
			$packages = json_decode(file_get_contents($installedFile));
			foreach ($packages->packages as $package) {
				if ($package->name == "plinct/cms") {
					$version =  $package->version;
				}
			}
		}
		return ['version'=> $version, 'commit'=> $commit] ;
	}
}
