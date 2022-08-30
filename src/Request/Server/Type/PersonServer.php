<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;

class PersonServer
{
    public function edit($params) {
        if ($params['birthDate'] == '') {
            unset($params['birthDate']);
        }

        CmsFactory::request()->api()->put('person', $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}