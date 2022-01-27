<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Structure\Main;

use Plinct\Cms\WebSite\Type\Structure\StructureViewInterface;
use Plinct\Cms\WebSite\WebSite;
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
        WebSite::addMain($content);
    }

    /**
     * @return array
     */
    public final function render(): array
    {
        return self::$mainElement->ready();
    }
}
