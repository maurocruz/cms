<?php

declare(strict_types=1);

namespace Plinct\Cms\Factory;

use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Structure\Main\MainView;

class ViewFactory
{
    /**
     * @param $content
     */
    public static function mainContent($content)
    {
        MainView::content($content);
    }

    /**
     * @param $content
     */
    public static function headerContent($content)
    {
        HeaderView::content($content);
    }

    public static function headerNavbar(string $title = null, array $list = null, int $level = null, array $searchInput = null)
    {
        HeaderView::navbar($title, $list, $level, $searchInput);
    }
}
