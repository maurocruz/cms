<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class EventServer
{
    public function erase(array $params): string
    {
        Api::delete("event", ['idevent'=>$params['id']]);

        return "/admin/event";
    }
}