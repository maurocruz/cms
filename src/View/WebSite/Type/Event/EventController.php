<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Event;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class EventController
{
	/**
	 * @param null $params
	 * @return null
	 */
  public function index($params = null) {
		return null;
  }
	/**
	 * @param $params
	 * @return bool
   */
  public function new($params = null): bool {
    return true;
  }
	/**
   * @param array $params
   * @return array
	 */
	public function edit(array $params): array
	{
	  $params = array_merge($params, [ "properties" => "*,location,superEvent,subEvent" ]);
	  return CmsFactory::request()->api()->get("event", $params)->ready();
	}
	/**
	 * @return void
	 */
  public function saveSitemap()
  {
    $dataSitemap = null;
    $data = CmsFactory::request()->api()->get("event", ['orderBy'=>'startDate desc','limit'=>'none'])->ready();
    foreach ($data as $value) {
      $dataSitemap[] = [
        "loc" => EventController . phpApp::getURL() . DIRECTORY_SEPARATOR . "eventos" . DIRECTORY_SEPARATOR . substr($value['startDate'],0,10) . DIRECTORY_SEPARATOR . urlencode($value['name']),
        "news" => [
          "name" => App::getTitle(),
          "language" => App::getLanguage(),
          "publication_date" => DateTime::formatISO8601($value['startDate']),
          "title" => $value['name']
        ]
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-event.xml"))->saveSitemap($dataSitemap, "news");
  }
}
