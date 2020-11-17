<?php


namespace Plinct\Cms\Controller;


use Plinct\Api\Type\Article;

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
}