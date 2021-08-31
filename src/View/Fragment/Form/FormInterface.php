<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Form;

interface FormInterface
{
    public function selectAdditionalType(string $class = "thing", string $value = null): array;

    public function selectCategory(string $class = "thing", string $value = null): array;
}