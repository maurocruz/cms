<?php

declare(strict_types=1);

namespace Plinct\Cms\Factory;

use Plinct\Cms\WebSite\Type\Structure\Header\HeaderView;
use Plinct\Cms\WebSite\Type\Structure\Main\MainView;
use Plinct\Cms\WebSite\Fragment\Fragment;

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
        $navbar = Fragment::navbar()->title($title);
        foreach ($list as $key => $value) {
            $navbar->newTab($key, $value);
        }
        $navbar->level($level);


        HeaderView::navbar($title, $list, $level, $searchInput);
    }
}
