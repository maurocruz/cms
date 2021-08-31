<?php

declare(strict_types=1);

namespace Plinct\Cms\Factory;

use Plinct\Cms\Template\TemplateController;
use Plinct\Cms\View\Structure\Main\MainView;

class TemplateFactory
{
    /**
     * @return TemplateController
     */
    public static function create(): TemplateController
    {
        return new TemplateController();
    }

    /**
     * @param $content
     */
    public static function mainContent($content)
    {
        MainView::content($content);
    }
}