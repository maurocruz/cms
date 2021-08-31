<?php

declare(strict_types=1);

namespace Plinct\Cms\Server;

use Plinct\Cms\App;
use Plinct\Tool\Curl;

class SoloineServer
{
    /**
     * @param array|null $params
     * @return string
     */
    public function get(array $params = null): string
    {
        return Curl::getUrlContents( App::getSoloineUrl() . "?" . http_build_query($params) );
    }
}