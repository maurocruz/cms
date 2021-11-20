<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\ListTable;

interface ListTableInterface
{
    /**
     * @param string $caption
     * @return ListTable
     */
    public function caption(string $caption): ListTable;

    /**
     * @param string ...$label
     * @return ListTable
     */
    public function labels(string ...$label): ListTable;

    /**
     * @param array $itemListElement
     * @param array $properties
     * @return ListTable
     */
    public function rows(array $itemListElement, array $properties): ListTable;

    /**
     * @param string|null $pathToEditButton
     * @return ListTable
     */
    public function setEditButton(string $pathToEditButton = null): ListTable;

    /**
     * @return array
     */
    public function ready(): array;
}