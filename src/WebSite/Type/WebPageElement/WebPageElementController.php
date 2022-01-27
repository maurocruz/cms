<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebPageElement;

use Plinct\Cms\Server\Api;
use Plinct\Cms\WebSite\Type\WebPage\WebPageController;

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
     */
    public function saveSitemap()
    {
        (new WebPageController())->saveSitemap();
    }
}