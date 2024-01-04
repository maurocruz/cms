<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\Event;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class EventController
{
	/**
	 * @param $params
	 * @return array
	 */
  public function index($params = null): array {
		$numberOfItemsData = CmsFactory::request()->api()->get('event',['fields'=>'COUNT(*) as count'])->ready();
    $params2 = array_merge([ "format" => "ItemList", "orderBy" => "dateModified desc, startDate desc"], $params);
		$data = CmsFactory::request()->api()->get("event", $params2)->ready();
	  $data['numberOfItems'] = $numberOfItemsData[0]['count'];
    return $data;
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
	  $params = array_merge($params, [ "properties" => "*,location,image,superEvent,subEvent" ]);
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
        "loc" => App::getURL() . DIRECTORY_SEPARATOR . "eventos" . DIRECTORY_SEPARATOR . substr($value['startDate'],0,10) . DIRECTORY_SEPARATOR . urlencode($value['name']),
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
