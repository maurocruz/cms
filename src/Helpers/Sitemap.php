<?php
namespace Plinct\Cms\Helpers;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Tool\DateTime;

class Sitemap
{
	/**
	 * @var string
	 */
	private string $type;

	private string $namespace = 'simple';

	private string $filename = 'sitemap.xml';

	private array $dataSitemap = [];
	/**
	 * @var array|null
	 */
	private ?array $sitemapExists = null;
	/**
	 * @var string|null
	 */
	private ?string $currentSitemap = null;

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * @param string $namespace
	 */
	public function setNamespace(string $namespace): void
	{
		$this->namespace = $namespace;
	}

	/**
	 * @param string $filename
	 */
	public function setFilename(string $filename): void
	{
		$this->filename = $filename;
	}

	/**
	 * @return string
	 */
	public function getFilename(): string
	{
		return $this->filename;
	}

	/**
	 * @param array $dataSitemap
	 */
	public function setDataSitemap(array $dataSitemap): void
	{
		$this->dataSitemap = $dataSitemap;
	}

	/**
	 * @return string|null
	 */
	public function getCurrentSitemap(): ?string
	{
		return $this->currentSitemap;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public static function buildSimpleSitemap(array $data): array
	{
		$dataSitemap = [];
		foreach ($data as $key => $item) {
			$typeBuilder = CmsFactory::helpers()->typeBuilder($item);
			$url = $item['url'] ?? null;
			$dateModified = $typeBuilder->getPropertyValue('dateModified');
			if ($url) {
				if (!str_contains($url, 'http')) {
					$url = CmsFactory::controller()->getHost() . $url;
				}
				$dataSitemap[$key]['loc'] = CmsFactory::helpers()->encodeUrlForSitemap($url);
				$dataSitemap[$key]['lastmod'] = DateTime::formatISO8601($dateModified);
			}
		}
		return $dataSitemap;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public static function buildNewsSitemap(array $data): array
	{
		$dataSitemap = [];
		foreach ($data as $item) {
			$typeBuilder = CmsFactory::helpers()->typeBuilder($item);
			$loc = CmsFactory::controller()->getHost()."/".substr($item['startDate'],0,10)."/".urlencode($item['name']);
			$dateModified = $typeBuilder->getPropertyValue('dateModified');
			$dateCreated = $typeBuilder->getPropertyValue('dateCreated');
			$dataSitemap[] = [
				"loc" => CmsFactory::helpers()->encodeUrlForSitemap($loc),
				'lastmod' => DateTime::formatISO8601($dateModified),
				"news" => [
					"name" => App::getTitle(),
					"language" => App::getLanguage(),
					"publication_date" => DateTime::formatISO8601($dateCreated),
					"title" => $item['name']
				]
			];
		}
		return $dataSitemap;
	}

	/**
	 * @param null $dir
	 * @return array
	 */
  public function getSitemaps($dir = null): array
  {
    $sitemaps = [];
    $root = $dir ?? $_SERVER['DOCUMENT_ROOT'];
    $handleRoot = opendir($root);
    while (false !== ($filename = readdir($handleRoot))) {
      $file = $root.DIRECTORY_SEPARATOR.$filename;
      if (is_file($file)) {
        $pathInfo = pathinfo($file);
        if ($pathInfo['extension'] === 'xml') {
          $sitemaps[] = $filename;
        }
      }
    }
    closedir($handleRoot);
		$this->sitemapExists = $sitemaps;
		return $sitemaps;
  }

	/**
	 * @param string|null $location
	 * @return false|string
	 */
	public function exist_sitemap(string $location = null): false|string
	{
		if (!$this->sitemapExists) {
			$this->getSitemaps($location);
		}
		$filename = "sitemap-".lcfirst($this->type).".xml";
		if (in_array($filename,$this->sitemapExists)) {
			return $filename;
		}
		return false;
	}

	/**
	 * @throws DOMException
	 */
	public function saveSitemap(): bool
	{
		$sitemap = new \Plinct\Tool\Sitemap($_SERVER['DOCUMENT_ROOT'].'/'.$this->filename);
		$sitemap->setNamespace($this->namespace);
		return $sitemap->saveSitemap($this->dataSitemap);
	}

}
