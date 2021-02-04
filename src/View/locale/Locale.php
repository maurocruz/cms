<?php

namespace Plinct\Cms\View\locale;

use Plinct\Cms\App;

class Locale
{
    /**
     * @param string|null $lang
     * @return string
     */
    public static function setTranslate(string $lang = null): string
    {
        $lang = $lang ?? App::getLanguage();
        putenv("LC_ALL=$lang");
        setlocale(LC_ALL, App::getLanguage() . ".utf8");
        bindtextdomain("fwc", __DIR__);
        textdomain("fwc");

        return $lang;
    }
}