<?php

declare(strict_types=1);

namespace Plinct\Cms\Server;

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