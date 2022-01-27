<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

interface ControllerInterface
{
    /**
     * @param $params
     * @return array
     */
    public function index($params = null): array;

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array;

    /**
     * @param $params
     * @return mixed
     */
    public function new($params = null);
}
