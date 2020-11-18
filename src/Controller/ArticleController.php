<?php


namespace Plinct\Cms\Controller;


use Plinct\Api\Type\Article;
use Plinct\Cms\App;
use Plinct\Tool\Sitemap;

class ArticleController implements ControllerInterface
{

    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "dateModified", "orderBy" => "datePublished", "ordering" => "desc" ];

        $params3 = $params ? array_merge($params, $params2) : $params2;

        return (new Article())->get($params3);
    }

    public function edit(array $params): array
    {
        $params2 = [ "properties" => "*,image,author" ];

        $params3 = $params ? array_merge($params, $params2) : $params2;

        $data = (new Article())->get($params3);

        return $data[0] ?? [];
    }

    public function new()
    {
        return null;
    }

    public function saveSitemap()
    {
        $dataSitemap = null;

        $params = [ "orderBy" => "datePublished", "ordering" => "desc", "limit" => "none" ];
        $data = (new Article())->get($params);

        foreach ($data as $value) {
            $dataSitemap[] = [
                "loc" => "http://" . filter_input(INPUT_SERVER, 'HTTP_HOST')
                    . DIRECTORY_SEPARATOR . "noticia"
                    . DIRECTORY_SEPARATOR . substr($value['datePublished'],0,10)
                    . DIRECTORY_SEPARATOR . urlencode($value['headline']),
                "name" => App::getTitle(),
                "language" => App::getLanguage(),
                "publication_date" => $value['datePublished'],
                "title" => $value['headline']
            ];
        }

        (new Sitemap("sitemap-news.xml"))->createSitemapNews($dataSitemap);
    }
}