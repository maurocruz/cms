<?php

namespace Plinct\Cms\View\Fragment\Icon;

interface IconInterface
{
    /**
     * @return string
     */
    public function home(): string;

    /**
     * @return string
     */
    public function plus(): string;

    /**
     * @return string
     */
    public function edit(): string;

    /**
     * @return string
     */
    public function delete(): string;
}