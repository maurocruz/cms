<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\ListTable;

interface ListTableInterface
{
    /**
     * @param string $caption
     * @return mixed
     */
    public function caption(string $caption);

    /**
     * @param ...$label
     * @return mixed
     */
    public function labels(...$label);

    /**
     * @param array $itemListElement
     * @param array $properties
     * @return mixed
     */
    public function rows(array $itemListElement, array $properties);

    /**
     * @param string|null $pathToEditButtom
     */
    public function setEditButton(string $pathToEditButtom = null): void;
    /**
     * @return array
     */
    public function ready(): array;
}