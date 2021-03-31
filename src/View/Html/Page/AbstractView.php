<?php
namespace Plinct\Cms\View\Html\Page;

abstract class AbstractView {
    protected $title;
    protected $description;
    protected $content;

    protected function addMain(array $content) {
        $this->content['main'][] = $content;
    }

    protected function addHeaderContent(array $content) {
        $this->content['contentHeader'][] = $content;
    }
}