<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

interface ViewInterface 
{
	/**
	 * @param $type
	 * @param $methodName
	 * @param $data
	 * @param $params
	 * @return mixed
	 */
    public function view($type, $methodName, $data, $params);

    /**
     * @param $content
     * @return mixed
     */
    public static function contentHeader($content);
}
