<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class EventController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "startDate", "ordering" => "desc" ], $params);
        return Api::get("event", $params2);
    }

    public function new($params = null): bool {
        return true;
    }

    public function edit(array $params): array {
        $params= array_merge($params, [ "properties" => "*,location,image" ]);
        return Api::get("event", $params);
    }

    public function saveSitemap() {
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
