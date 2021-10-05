<?php

declare(strict_types=1);

namespace Plinct\Cms\View;

abstract class ViewAbstract
{
    /**
     *
     */
    protected const NAMESPACE_VIEW = "\\Plinct\\Cms\\View\\Types\\";
    /**
     * @var string
     */
    protected string  $title;
    /**
     * @var string
     */
    protected string $description;
    /**
     * @var array
     */
    protected array $content;

    /**
     * @param array $content
     */
    protected function addMain(array $content) {
        $this->content['main'][] = $content;
    }

    /**
     * @param array $content
     */
    protected function addHeaderContent(array $content) {
        $this->content['contentHeader'][] = $content;
    }
}