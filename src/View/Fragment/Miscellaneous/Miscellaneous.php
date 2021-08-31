<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Miscellaneous;

class Miscellaneous implements MiscellaneousInterface
{
    /**
     * @param string $message
     * @param array|string[] $attributes
     * @return array
     */
    public function message(string $message = "No content", array $attributes = ['class'=>'warning']): array
    {
        return ['tag'=>'p','attributes'=>$attributes,'content'=>_($message)];
    }
}