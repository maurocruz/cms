<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Miscellaneous;

interface MiscellaneousInterface
{
    /**
     * @param string $message
     * @param array|string[] $attributes
     * @return array
     */
    public function message(string $message, array $attributes = ['class'=>'warning']): array;
}