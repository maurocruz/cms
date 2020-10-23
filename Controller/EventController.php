<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\Event;
use Plinct\Cms\App;
use Plinct\Tool\Sitemap;

class EventController implements ControllerInterface
{
    public function index($params = null): array
    {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "startDate", "ordering" => "desc" ], $params);

        return (new Event())->get($params2);
    }

    public function new()
    {
        return true;
    }

    public function edit(array $params): array
    {
        $params= array_merge($params, [ "properties" => "*,location,image" ]);

        return (new Event())->get($params);
    }

    public function saveSitemap()
    {
        $dataSitemap = null;

        $params = [ "orderBy" => "startDate", "ordering" => "desc", "limit" => "none" ];
        $data = (new Event())->get($params);

        foreach ($data as $value) {
            $dataSitemap[] = [
                "loc" => "http://" . filter_input(INPUT_SERVER, 'HTTP_HOST')
                . DIRECTORY_SEPARATOR . "eventos"
                . DIRECTORY_SEPARATOR . substr($value['startDate'],0,10)
                . DIRECTORY_SEPARATOR . urlencode($value['name']),
                "name" => App::getTitle(),
                "language" => App::getLanguage(),
                "publication_date" => $value['startDate'],
                "title" => $value['name']
            ];
        }

        (new Sitemap("sitemap-event.xml"))->createSitemapNews($dataSitemap);
    }
}
