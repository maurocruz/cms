<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

interface TemplateInterface
{
    /**
     * @param array|null $params
     * @param array|null $queryStrings
     * @return string
     */
    public function viewContent(array $params = null, array $queryStrings = null): string;

    /**
     * @param null $auth
     * @return string
     */
    public function login($auth = null): string;
}