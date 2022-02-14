<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\ListTable;

class ListTable extends ListTableAbstract implements ListTableInterface
{
    /**
     * @param array|string[] $attributes
     */
    public function __construct(array $attributes = null)
    {
        parent::__construct($attributes);
    }

    /**
     * @param string $caption
     * @return ListTable
     */
    public function caption(string $caption): ListTable
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param string ...$label
     * @return ListTable
     */
    public function labels(string ...$label): ListTable
    {
        $this->labels = func_get_args();
        return $this;
    }

    /**
     * @param ...$list
     * @return ListTableInterface
     */
    public function addRow(...$list): ListTableInterface
    {
        $this->rows[] = func_get_args();
        return $this;
    }

    public function buttonEdit(string $path): ListTableInterface
    {
        $this->buttonEdit[] = $path;
        return $this;
    }

    public function buttonDelete(string $idIsPartOf, string $tableIsPartOf, string $idHasPart = null, string $tableHasPart = null): ListTableInterface
    {
        $this->idIsPartOf = $idIsPartOf;
        $this->tableIsPartOf = $tableIsPartOf;
        $this->idHasPart = $idHasPart;
        $this->tableHasPart = $tableHasPart;
        $this->buttonDelete = true;
        return $this;
    }

    /**
     * @param array $itemListElement
     * @param array $properties
     * @return ListTable
     */
    public function rows(array $itemListElement, array $properties): ListTable
    {
        $this->properties = $properties;
        $this->itemListElement = $itemListElement;
        return $this;
    }

    /**
     * @param string|null $pathToEditButton
     * @return $this
     */
    public function setEditButton(string $pathToEditButton = null): ListTable
    {
        $this->editButton = true;
        $this->pathToEditButton = $pathToEditButton;
        return $this;
    }

    /**
     * @return array
     */
    public function ready(): array
    {
        // LABELS COLUMNS
        $this->buildLabels();
        // ROWS
        $this->buildRows();
        // CAPTION
        $this->buildCaption();
        // READY
        return $this->table->ready();
    }
}
