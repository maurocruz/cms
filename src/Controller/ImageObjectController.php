<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Server\PDOConnect;
use Plinct\Api\Type\ImageObject;

class ImageObjectController implements ControllerInterface
{
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "groupBy" => "keywords", "limit" => "none", "orderBy" => "uploadDate desc, name" ];

        $params3 = $params ? array_merge($params2, $params) : $params2;

        return (new ImageObject())->get($params3);
    }

    public function keywords($params)
    {
        $keywords = urldecode($params['id']);

        $params2 = [ "format" => "ItemList", "keywords" => $keywords, "limit" => "none", "orderBy" => "uploadDate desc, name" ];

        return(new ImageObject())->get($params2);
    }

    public function new()
    {
        // TODO: Implement new() method.
    }

    public function edit(array $params): array
    {
        // TODO: Implement edit() method.
    }
}