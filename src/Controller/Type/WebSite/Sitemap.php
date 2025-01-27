<?php
namespace Plinct\Cms\Controller\Type\WebSite;

use DOMException;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;
use Plinct\Tool\DateTime;
use Plinct\Tool\ToolBox;

class Sitemap
{
	/**
	 * @var string
	 */
	private string $type;
	/**
	 * @var ?array
	 */
	private ?array $params;

	private ?array $currentSitemaps = null;

	public function __construct(string $type, array $params = null)
	{
		$this->type = $type;
		$this->params = $params;
	}

	/**
	 * @param array $params
	 */
	public function setParams(array $params): void
	{
		$this->params = $params;
	}

  /**
   * @param null $dir
   * @return array
   */
  public function getSitemaps($dir = null): array
  {
    $sitemaps = null;
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
		$this->currentSitemaps = $sitemaps;
		return $sitemaps;
  }

	public function exist_sitemap(string $location = null)
	{
		if (!$this->currentSitemaps) {
			$this->getSitemaps($location);
		}
		$filename = "sitemap-".lcfirst($this->type).".xml";
		if (in_array($filename,$this->currentSitemaps)) {
			return $filename;
		}
		return false;
	}

	/**
	 * @return void
	 * @throws DOMException
	 */
	public function saveSitemap() {
		$type = lcfirst($this->params['type']);
		if (!CmsFactory::controller()->type($type)->setMethod('sitemap')->setParams($this->params)->ready()) {
			$data = CmsFactory::model()->api()->get($type,['limit'=>'none','orderBy'=>'dateModified','ordering'=>'desc'])->ready();
			foreach ($data as $value) {
				$typeBuilder = ToolBox::typeBuilder($value);
				$id = $typeBuilder->getId();
				$dateModified = $typeBuilder->getPropertyValue('dateModified');
				$image = $value['image'] ?? null;
				//
				$dataSitemap[] = [
					'loc' => App::getURL() . "/t/$type/$id",
					'lastmod' => DateTime::formatISO8601($dateModified),
					'image' => $image
				];
			}
			(new \Plinct\Tool\Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-$type.xml"))->saveSitemap($dataSitemap);
		}
	}
}
