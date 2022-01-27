<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Box;

use Plinct\Web\Element\Element;

class Box implements BoxInterface
{
    /**
     * @param $content
     * @param string|null $caption
     * @return array
     */
    public function simpleBox($content, string $caption = null): array
    {
        $div = new Element('div',['class'=>'box']);
        if ($caption) $div->content("<h4>$caption</h4>");
        $div->content($content);
        return $div->ready();
    }

    /**
     * @param string $caption
     * @param $content
     * @return array
     */
    public function expandingBox(string $caption, $content): array
    {
        $id = "form-expanding-". mt_rand(111,999);

        $div = new Element('div',['id'=> $id, 'class'=>'box box-expanding']);
        // CAPTION
        $div->content("<h4 class='button-dropdown button-dropdown-contracted' onclick='expandBox(this,\"$id\");'>$caption</h4>");
        // CONTENT
        $div->content($content);
        // READY
        return $div->ready();
    }
}
