<?php
declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\ListTable;

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
     */
    public function caption(string $caption)
    {
        $this->caption = $caption;
    }

    /**
     * @param ...$label
     */
    public function labels(...$label)
    {
        $this->labels = func_get_args();
    }

    /**
     * @param array $itemListElement
     * @param array $properties
     */
    public function rows(array $itemListElement, array $properties)
    {
        $this->properties = $properties;
        $this->rows = $itemListElement;
    }


    /**
     *
     */
    public function setEditButton(string $pathToEditButtom = null): void
    {
        $this->editButton = true;
        $this->pathToEditButtom = $pathToEditButtom;
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