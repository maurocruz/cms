<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\ImageObject;
use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\Server\ImageObjectServer;

class ImageObjectController implements ControllerInterface
{
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "groupBy" => "keywords", "limit" => "none", "orderBy" => "keywords" ];

        $params3 = $params ? array_merge($params2, $params) : $params2;

        return (new ImageObject())->get($params3);
    }

    public function keywords($params)
    {
        $keywords = urldecode($params['id']);

        $params2 = [ "format" => "ItemList", "keywords" => $keywords, "limit" => "none", "orderBy" => "uploadDate desc, keywords" ];

        $data['list'] = (new ImageObject())->get($params2);
        $data['paramsUrl'] = $params;

        return $data;
    }

    public function new()
    {
        return null;
    }

    public function edit(array $params): array
    {
        $params2 = [ "properties" => "*,author" ];

        $params3 = array_merge($params2, $params);

        $data = (new ImageObject())->get($params3);

        $value = $data[0];
        $id = PropertyValue::extractValue($value['identifier'], "id");
        $value['info'] = (new ImageObjectServer())->getImageHasPartOf($id);

        return $value;
    }
}