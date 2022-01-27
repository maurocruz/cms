<?php
declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\ListTable;

// TODO fazer link para ordenar listagem pelo rótulo de coluna

// TODO fazer botão excluir

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
     * @param array $itemListElement
     * @param array $properties
     * @return ListTable
     */
    public function rows(array $itemListElement, array $properties): ListTable
    {
        $this->properties = $properties;
        $this->rows = $itemListElement;
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
        // CAPTION
        $this->buildCaption();

        // LABELS COLUMNS
        $this->buildLabels();

        // ROWS
        $this->buildRows();

        // READY
        return $this->table->ready();
    }
}