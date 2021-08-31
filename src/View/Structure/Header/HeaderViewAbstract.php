<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Structure\Header;

abstract class HeaderViewAbstract
{
    protected static $HeaderElement = null;

    /**
     * @param $content
     */
    public static function content($content) {
        self::$HeaderElement->content($content);
    }
}