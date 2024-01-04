<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Article;

use DOMException;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class ArticleController
{
  /**
   * @param null $params
   * @return array
   */
  public function index($params = null): array
  {
    $params2 = [ "format" => "ItemList", "properties" => "dateModified", "orderBy" => "dateModified desc, datePublished desc" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
		return CmsFactory::request()->api()->get('article', $params3)->ready();
  }

  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $params2 = [ "properties" => "*,image,author" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
		if (isset($params3['idarticle'])) {
			return CmsFactory::request()->api()->get("article", $params3)->ready();
		}
		return [];
  }

  public function new($params = null) {
    return null;
  }

	/**
	 * @throws DOMException
	 */
	public function saveSitemap() {
    $dataSitemap = null;
    $params = [ "orderBy" => "datePublished", "ordering" => "desc" ];
    $data = CmsFactory::request()->api()->get("article", $params)->ready();
    foreach ($data as $value) {
      if ($value['datePublished']) {
        $dataSitemap[] = [
          "loc" => App::getURL() . DIRECTORY_SEPARATOR . "noticia" . DIRECTORY_SEPARATOR . substr($value['datePublished'], 0, 10) . DIRECTORY_SEPARATOR . urlencode($value['headline']),
          "news" => [
            "name" => App::getTitle(),
            "language" => App::getLanguage(),
            "publication_date" => DateTime::formatISO8601($value['datePublished']),
            "title" => $value['headline']
          ]
        ];
      }
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-article.xml"))->saveSitemap($dataSitemap, "news");
  }
}
