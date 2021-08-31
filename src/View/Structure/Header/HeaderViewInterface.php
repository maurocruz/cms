<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Structure\Header;

interface HeaderViewInterface
{
    public static function ready(): array;

    public static function userBar();

    public static function navbar(string $title, array $list = null, int $level = 0, array $searchInput = null);

    public static function content($content);

    public static function titleSite();
}