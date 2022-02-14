<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\ListTable;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

abstract class ListTableAbstract
{
    /**
     * @var Table
     */
    protected Table $table;
    /**
     * @var ?string
     */
    protected ?string $caption = null;
    /**
     * @var array
     */
    protected array $labels = [];
    /**
     * @var array
     */
    protected array $rows = [];
    /**
     * @var ?array
     */
    protected ?array $buttonEdit = null;
    /**
     * @var bool
     */
    protected bool $buttonDelete = false;
    /**
     * @var array
     */
    protected array $itemListElement = [];
    /**
     * @var array
     */
    protected array $properties;
    /**
     * @var bool
     */
    protected ?bool $editButton = null;
    /**
     * @var ?string
     */
    protected ?string $pathToEditButton = null;

    protected string $idIsPartOf;
    protected string $tableIsPartOf;
    protected string $idHasPart;
    protected string $tableHasPart;

    /**
     * @param array|string[] $attributes
     */
    public function __construct(array $attributes = null)
    {
        $this->table = new Table($attributes);
    }

    /**
     * @return void
     */
    protected function buildCaption()
    {
        $numberOfItems = $this->table->getNumberOfRows();

        $caption = "<h1>$this->caption</h1>";
        $caption .= "<p>" . sprintf(_("Showing %s items!"), "<span>$numberOfItems</span>") . "</p>";

        $this->table->caption($caption);
    }

    /**
     * @return void
     */
    protected function buildLabels()
    {
        if ($this->editButton || $this->buttonEdit) {
            $this->table->head(_("Edit"), ['style'=>'width: 50px;']);
        }

        foreach ($this->labels as $columnLabel) {
            $this->table->head("<a href='#'>$columnLabel</a>");
        }

        if ($this->buttonDelete) {
            $this->table->head(_("Delete"), ['style'=>'width: 50px;']);
        }
    }

    /**
     *
     */
    protected function buildRows()
    {
        if (!empty($this->itemListElement)) {
            foreach ($this->itemListElement as $itemList) {
                $item = $itemList['item'] ?? $itemList;
                $id = ArrayTool::searchByValue($item['identifier'], 'id', 'value');

                if ($this->editButton || $this->buttonEdit) {
                    $this->table->bodyCell(Fragment::icon()->edit(), ['style' => 'text-align: center;'], ($this->buttonEdit ?? $this->pathToEditButton) . $id);
                }

                foreach ($this->properties as $property) {
                    $explode = explode(":",$property);
                    $bodyCell = $item;
                    foreach ($explode as $prop) {
                        if(is_array($bodyCell) && array_key_exists($prop, $bodyCell)) {
                            $bodyCell = $bodyCell[$prop]['name'] ?? $bodyCell[$prop];
                        }
                    }
                    $this->table->bodyCell($bodyCell);
                }

                if ($this->buttonDelete) {
                    $this->table->bodyCell(Fragment::icon()->delete(), ['style' => 'text-align: center;'], $this->buttonDelete . $id);
                }

                $this->table->closeRow();
            }
        }

        // AS ADD ROW
        if (!empty($this->rows)) {
            foreach ($this->rows as $key => $row) {
                // edit buttom
                if ($this->buttonEdit) {
                    $this->table->bodyCell(Fragment::icon()->edit(), ['style' => 'text-align: center;'], $this->buttonEdit[$key]);
                }
                // items
                foreach ($row as $cell) {
                    $this->table->bodyCell($cell);
                }
                // delete buttom
                if ($this->buttonDelete) {
                    $this->table->bodyCell(Fragment::button()->buttonDelete($this->idIsPartOf, $this->tableIsPartOf, $this->idHasPart, $this->tableHasPart),['style' => 'text-align: center;']);
                }
                // close
                $this->table->closeRow();
            }

        }
        // NO ITEMS FOUND
        if ($this->table->getNumberOfRows() === 0) {
            $countLabels = count($this->labels);
            $colspan = $this->editButton ? $countLabels + 1 : $countLabels;
            $this->table->bodyCell(_("No items found!"),['colspan'=>"$colspan",'style'=>'text-align: center; font-size:120%; font-weight: bold; color: yellow;'])->closeRow();
        }
    }
}
