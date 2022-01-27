<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Structure;

interface StructureViewInterface
{
    public function create();

    public static function content($content = null);

    public function render(): array;
}