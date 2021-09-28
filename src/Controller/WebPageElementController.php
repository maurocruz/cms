<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class WebPageElementController
{
    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params2 = [ "properties" => "*" ];
        $params3 = array_merge($params, $params2);
        $data = Api::get("webPageElement", $params3);
        return $data[0];
    }

    /**
     * @param null $params
     */
    public function saveSitemap($params = null)
    {
        (new WebPageController())->saveSitemap();
    }
}