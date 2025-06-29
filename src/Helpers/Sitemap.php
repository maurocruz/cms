<?php
namespace Plinct\Cms\Helpers;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;

class Sitemap
{
	/**
	 * @var string
	 */
	private string $type;
	/**
	 * @var ?array
	 */
	private ?array $params = null;
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
	 * @param ?array $params
	 */
	public function setParams(?array $params = null): void
	{
		$this->params = $params;
	}

	/**
	 * @return string|null
	 */
	public function getCurrentSitemap(): ?string
	{
		return $this->currentSitemap;
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
	 * @return bool
	 * @throws Exception
	 */
	public function saveSitemap(): bool
	{
		$dataSitemap = [];
		$data = CmsFactory::model()->type($this->type)->get($this->params ?? ['limit'=>'none','orderBy'=>'dateModified','ordering'=>'desc']);
		foreach ($data as $key => $item) {
			$typeBuilder = CmsFactory::helpers()->typeBuilder($item);
			$url = $item['url'] ?? null;
			$dateModified = $typeBuilder->getPropertyValue('dateModified');
			if ($url) {
				if (!str_contains($url,'http')) {
					$url = CmsFactory::controller()->getHost().$url;
				}
				$dataSitemap[$key]['loc'] = $this->encodeUrlForSitemap($url);
				$dataSitemap[$key]['lastmod'] =  DateTime::formatISO8601($dateModified);
			}
		}
		$this->currentSitemap = "/sitemap-$this->type.xml";
		return (new \Plinct\Tool\Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-$this->type.xml"))->saveSitemap($dataSitemap);
	}

	/**
	 * @param $url
	 * @return string
	 */
	private function encodeUrlForSitemap($url): string
	{
		$parsed = parse_url($url);
		$scheme = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
		$host   = $parsed['host'] ?? '';
		$port   = isset($parsed['port']) ? ':' . $parsed['port'] : '';
		$path   = isset($parsed['path']) ? implode('/', array_map('rawurlencode', explode('/', $parsed['path']))) : '';
		$query  = '';
		if (isset($parsed['query'])) {
			parse_str($parsed['query'], $queryParams);
			$query = '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
		}
		return $scheme . $host . $port . '/' . ltrim($path, '/') . $query;
	}

}
