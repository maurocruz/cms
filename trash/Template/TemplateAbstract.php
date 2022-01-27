<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

use Plinct\Tool\Locale;

abstract class TemplateAbstract
{
    /**
     * @var array
     */
    protected $html;
    /**
     * @var string[]
     */
    protected $head;
    /**
     * @var string[]
     */
    protected $body;
    /**
     * @var array
     */
    protected $wrapper;
    /**
     * @var array
     */
    protected $container;
    /**
     * @var array
     */
    protected $content;
    /**
     * @var array
     */
    protected $contentHeader;
    /**
     * @var array
     */
    protected $contentFooter;
    /**
     * @var array
     */
    protected $aside;
    /**
     * @var array
     */
    protected $footer;

    /**
     *
     */
    public function __construct()
    {
        $this->html = ["tag" => "html", "attributes" => ["lang" => Locale::getServerLanguage()]];
        $this->head = ["tag" => "head"];
        $this->body = ["tag" => "body"];
        $this->wrapper = ["tag" => "wrapper", "attributes" => ["class" => "wrapper"]];
        $this->container = ["tag" => "div", "attributes" => ["class" => "container"]];
        $this->content = ["tag" => "div", "attributes" => ["class" => "content"]];
        $this->aside = ["tag" => "aside", "attributes" => ["class" => "aside"]];
        $this->contentHeader = ["tag" => "header", "attributes" => ["class" => "content-header"]];
        $this->contentFooter = ["tag" => "footer", "attributes" => ["class" => "content-footer"]];
        $this->footer = ["tag" => "footer", "attributes" => ["class" => "footer"]];
    }

    /**
     * @param string $element
     * @param $content
     * @param int|null $position
     */
    protected function append(string $element, $content, int $position = null)
    {
        $_element = $this->{$element};
        $newContent = [];
        if (is_numeric($position) && isset($_element['content'])) {
            $newContent[$position] = $content;
            foreach ($_element['content'] as $key => $value) {
                if ($key >= $position) {
                    $key = $key + 1;
                }
                $newContent[$key] = $value;
            }
            ksort($newContent);
            $this->{$element}['content'] = $newContent;
        } else {
            $this->{$element}['content'][] = $content;
        }
    }

    /**
     *
     */
    protected function simpleMain()
    {
        // CONTENT FOOTER
        $this->content['content'][] = $this->footer;
        // CONTAINER
        $this->body['content'][] = $this->content;
        // HEAD
        $this->html['content'][] = $this->head;
        //BODY
        $this->html['content'][] = $this->body;
    }
}
