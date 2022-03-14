<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Event;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Cms\WebSite\Type\ControllerInterface;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class EventController implements ControllerInterface
{
	/**
	 * @param $params
	 * @return array
	 */
    public function index($params = null): array
    {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "startDate", "ordering" => "desc" ], $params);
        return Api::get("event", $params2);
    }

	/**
	 * @param $params
	 * @return bool
	 */
    public function new($params = null): bool
    {
        return true;
    }

	/**
	 * @param array $params
	 * @return array
	 */
    public function edit(array $params): array
    {
      $params= array_merge($params, [ "properties" => "*,location,image,subEvent" ]);
      return Api::get("event", $params);
    }

	/**
	 * @return void
	 */
    public function saveSitemap()
    {
        $dataSitemap = null;
        $params = [ "orderBy" => "startDate desc" ];
        $data = Api::get("event", $params);
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
        (new Sitemap("sitemap-event.xml"))->saveSitemap($dataSitemap, "news");
    }
}
