<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Request\Api;

class PersonServer
{
    public function edit($params) {
        if ($params['birthDate'] == '') {
            unset($params['birthDate']);
        }

        Api::put('person', $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}