<?php

declare(strict_types=1);

namespace Plinct\Cms\Factory;

use Plinct\Cms\Server\SoloineServer;

class ServerFactory
{
    /**
     * @return SoloineServer
     */
    public static function soloine(): SoloineServer
    {
        return new SoloineServer();
    }
}