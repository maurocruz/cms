<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Server;

use Plinct\Cms\Controller\App;
use Plinct\Tool\Curl;

class SoloineServer
{
    /**
     * @param array|null $params
     * @return string
     */
    public function get(array $params = null): string
    {
        if (filter_var(App::getSoloineUrl(), FILTER_VALIDATE_URL)) {
            return Curl::getUrlContents(App::getSoloineUrl() . "?" . http_build_query($params));
        } else {
            return json_encode([
                'status' => 'fail',
                'message' => 'Error: Soloine url api not set!'
            ]);
        }
    }
}
