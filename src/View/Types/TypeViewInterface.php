<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types;

interface TypeViewInterface
{
    /**
     * @param array $data
     */
    public function index(array $data);

    /**
     * @param null $data
     */
    public function new($data = null);

    /**
     * @param array $data
     */
    public function edit(array $data);
}