<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\Fragment\ListTable;

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
     * @param ...$list
     * @return ListTableInterface
     */
    public function addRow(... $list): ListTableInterface;

    /**
     * @param string $path
     * @return ListTableInterface
     */
    public function buttonEdit(string $path): ListTableInterface;

    /**
     * @param string $idIsPartOf
     * @param string $tableIsPartOf
     * @param string|null $idHasPart
     * @param string|null $tableHasPart
     * @return ListTableInterface
     */
    public function buttonDelete(string $idIsPartOf, string $tableIsPartOf, string $idHasPart = null, string $tableHasPart = null): ListTableInterface;

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
