<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\ListTable;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

abstract class ListTableAbstract
{
    /**
     * @var Table
     */
    protected Table $table;
    /**
     * @var string
     */
    protected string $caption;
    /**
     * @var array
     */
    protected array $labels = [];
    /**
     * @var array
     */
    protected array $rows;
    /**
     * @var array
     */
    protected array $properties;
    /**
     * @var bool
     */
    protected ?bool $editButton = null;
    /**
     * @var string
     */
    protected string $pathToEditButtom;

    /**
     * @param array|string[] $attributes
     */
    public function __construct(array $attributes = null)
    {
        $this->table = new Table($attributes);
    }

    protected function buildCaption()
    {
        // count
        $numberOfItems = is_array($this->rows) ? count($this->rows) : "ND";

        $caption = "<h1>$this->caption</h1>";
        $caption .= "<p>" . sprintf(_("Showing %s items!"), "<span>$numberOfItems</span>") . "</p>";

        $this->table->caption($caption);
    }

    /**
     *
     */
    protected function buildLabels()
    {
        if ($this->editButton) {
            $this->table->head(_("Edit"), ['style'=>'width: 50px;']);
        }
        foreach ($this->labels as $columnLabel) {
            if(is_array($columnLabel)) {
                $key = key($columnLabel);
                $value = $columnLabel[$key];
                $this->table->head($value, null, $key);
            } else {
                $this->table->head($columnLabel);
            }
        }
    }

    /**
     *
     */
    protected function buildRows()
    {
        if (!empty($this->rows)) {
            foreach ($this->rows as $itemList) {
                $item = $itemList['item'];
                $id = ArrayTool::searchByValue($item['identifier'], 'id', 'value');

                if ($this->editButton) {
                    $this->table->bodyCell(Fragment::icon()->edit(), ['style' => 'text-align: center;'], $this->pathToEditButtom . $id);
                }

                foreach ($this->properties as $property) {
                    if (array_key_exists($property, $item)) {
                        $this->table->bodyCell($item[$property]);
                    }
                }

                $this->table->closeRow();
            }
        } else {
            $countLabels = count($this->labels);
            $colspan = $this->editButton ? $countLabels + 1 : $countLabels;
            $this->table->bodyCell(_("No items found!"),['colspan'=>"$colspan",'style'=>'text-align: center; font-size:120%; font-weight: bold; color: yellow;'])->closeRow();
        }
    }
}
