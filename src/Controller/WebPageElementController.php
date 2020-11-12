<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\WebPageElement;

class WebPageElementController implements ControllerInterface
{
    public function index($params = null): array
    {
        return [];
    }

    public function new()
    {
        return null;
    }

    public function edit(array $params): array
    {
        $params2 = [ "properties" => "*" ];

        $params3 = array_merge($params, $params2);

        $data = (new WebPageElement())->get($params3);

        return $data[0];
    }
}