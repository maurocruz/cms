<?php
namespace Plinct\Cms\Application\Context;

use Gitonomy\Git\Repository;
use Plinct\Cms\Domain\Cache\CacheInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GetRepo
{
	private CacheInterface $cache;
	private string $basedir;

	public function __construct(CacheInterface $cache, ContainerInterface $container)
	{
		$this->cache = $cache;
		$settings = [];
		try {
			$settings = $container->get('settings');
		}  catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
			error_log($e->getMessage());
		}
		$this->basedir = isset($settings['basedir']) && is_string($settings['basedir']) ? $settings['basedir'] : __DIR__ . "/../../../../";
	}

	public function getRepo(): array
	{
		$repoCacheKey = $this->cache->setKey('repo', 'version', []);
		if ($this->cache->has($repoCacheKey)) {
			return $this->cache->get($repoCacheKey);
		}

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
				$commit = rtrim((string)preg_replace("/(.*?\/){2}/", '', $tags[0]->getFullname()));
			} else {
				$commit = substr($commitHash,0,8);
			}
			$version = $revision;

		} else {
			$installedFile = realpath($_SERVER['DOCUMENT_ROOT'] . "/../vendor/composer/installed.json");
			if ($installedFile) {
				$json = file_get_contents($installedFile);
				if (is_string($json) && $json !== '') {
					$packages = json_decode($json);
					if (is_object($packages) && isset($packages->packages) && is_iterable($packages->packages)) {
						foreach ($packages->packages as $package) {
							if (is_object($package) && ($package->name ?? null) === "plinct/cms") {
								$pkgVersion = $package->version ?? null;
								if (is_string($pkgVersion) && $pkgVersion !== '') {
									$version = $pkgVersion;
								}
								break;
							}
						}
					}
				}
			}
		}

		$result = ['version' => $version, 'commit' => $commit];
		$this->cache->set($repoCacheKey, $result, 86400);

		return $result;
	}
}
