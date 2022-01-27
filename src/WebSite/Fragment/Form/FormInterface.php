<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Form;

use Plinct\Web\Element\Form\Form as WebForm;

interface FormInterface
{
    /**
     * @param string $class
     * @param string|null $value
     * @return mixed
     */
    public function selectAdditionalType(string $class = "thing", string $value = null);

    /**
     * @param string $class
     * @param string|null $value
     * @return mixed
     */
    public function selectCategory(string $class = "thing", string $value = null);

    /**
     * @param string $action
     * @param string $name
     * @param string|null $value
     * @return array
     */
    public function search(string $action, string $name, string $value = null): array;

    /**
     * @param array|null $attributes
     * @return WebForm
     */
    public function create(array $attributes = null): WebForm;

    /**
     * @param string $property
     * @param $typesForChoose
     * @param $value
     * @param string $nameLike
     * @param array $attributes
     * @return array
     */
    public function chooseType(string $property, $typesForChoose, $value, string $nameLike = "name", array $attributes = []) : array;
}
