<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Article;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class ArticleController
{
  /**
   *
   */
  public function index()
  {
		return null;
  }
  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $params2 = [ "properties" => "*,author" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
		if (isset($params3['idarticle'])) {
			return CmsFactory::request()->api()->get("article", $params3)->ready();
		}
		return [];
  }
	/**
	 * @param $params
	 * @return null
	 */
  public function new($params = null) {
    return null;
  }
	/**
	 */
	public function saveSitemap() {
    $dataSitemap = null;
    $data = CmsFactory::request()->api()->get("article", ['orderBy'=>'datePublished','ordering'=>'desc','limit'=>'none'])->ready();
    foreach ($data as $value) {
      if ($value['datePublished']) {
        $dataSitemap[] = [
          "loc" => ArticleController . phpApp::getURL() . DIRECTORY_SEPARATOR . "noticia" . DIRECTORY_SEPARATOR . substr($value['datePublished'], 0, 10) . DIRECTORY_SEPARATOR . urlencode($value['headline']),
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
