<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

interface ViewInterface 
{
    /**
     * @param $type
     * @param $methodName
     * @param $data
     * @return mixed
     */
    public function view($type, $methodName, $data);

    /**
     * @param $content
     * @return mixed
     */
    public static function contentHeader($content);
}
