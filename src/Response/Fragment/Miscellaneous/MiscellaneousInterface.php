<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\Fragment\Miscellaneous;

interface MiscellaneousInterface
{
    /**
     * @param string $message
     * @param array|string[] $attributes
     * @return array
     */
    public function message(string $message, array $attributes = ['class'=>'warning']): array;

    /**
     * @param $data
     * @return array
     */
    public function sitemap($data): array;
}