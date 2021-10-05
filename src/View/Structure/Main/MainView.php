<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Structure\Main;

use Plinct\Cms\View\Structure\StructureViewInterface;
use Plinct\Web\Element\Element;

class MainView implements StructureViewInterface
{
    private static Element $mainElement;

    /**
     *
     */
    public function create()
    {
        self::$mainElement = new Element('main',['class'=>'main','id'=>'main']);
    }

    /**
     * @param null $content
     */
    public static function content($content = null)
    {
        self::$mainElement->content($content);
    }

    /**
     * @return array
     */
    public final function render(): array
    {
        return self::$mainElement->ready();
    }
}
