<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

abstract class ViewAbstract
{
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