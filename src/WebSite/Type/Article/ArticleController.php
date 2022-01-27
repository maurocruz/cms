<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Article;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Cms\WebSite\Type\ControllerInterface;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class ArticleController implements ControllerInterface
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "dateModified", "orderBy" => "dateModified desc, datePublished desc" ];
        $params3 = $params ? array_merge($params, $params2) : $params2;
        return Api::get("article", $params3);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params2 = [ "properties" => "*,image,author" ];
        $params3 = $params ? array_merge($params, $params2) : $params2;
        return Api::get("article", $params3);
    }

    public function new($params = null) {
        return null;
    }

    public function saveSitemap() {
        $dataSitemap = null;
        $params = [ "orderBy" => "datePublished", "ordering" => "desc" ];
        $data = Api::get("article", $params);
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
        (new Sitemap("sitemap-article.xml"))->saveSitemap($dataSitemap, "news");
    }
}
