<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Web\Element\Form as WebForm;

interface FormInterface
{
    public function selectAdditionalType(string $class = "thing", string $value = null): array;

    public function selectCategory(string $class = "thing", string $value = null): array;

    public function search(string $action, string $name, string $value = null): array;

    public function create(array $attributes = null): WebForm;
}