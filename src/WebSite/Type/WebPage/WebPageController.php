<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebPage;

use Plinct\Cms\App;
use Plinct\Cms\Request\Api;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class WebPageController
{
    /**
     * @param array|null $params
     * @return array
     */
    public function index(?array $params = []): array
    {
        $params2 = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ], $params);
        return Api::get("webPage", $params2);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params2 = array_merge($params, [ "properties" => "*,hasPart,isPartOf" ]);
        $data = Api::get("webPage", $params2);
        return $data[0];
    }

    /**
     * @return array
     */
    public function sitemap(): array
    {
        return (new \Plinct\Cms\Server\Sitemap())->getSitemaps();
    }

    /**
     */
    public function saveSitemap()
    {
        $dataSitemap = null;
        $data = Api::get("webPage", ["properties" => "url,dateModified", "orderBy" => "dateModified desc"]);

        foreach ($data as $value) {
            $dataSitemap[] = [
                "loc" => App::getURL() . $value['url'],
                "lastmod" => DateTime::formatISO8601($value['dateModified'])
            ];
        }

        (new Sitemap("sitemap-webPage.xml"))->saveSitemap($dataSitemap);
    }
}
