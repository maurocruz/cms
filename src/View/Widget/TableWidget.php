<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Widget;

use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

class TableWidget
{
    /**
     * @var Table
     */
    private Table $table;
    /**
     * @var string|null
     */
    private ?string $tableHasPart;
    /**
     * @var string|null
     */
    private ?string $caption = null;
    /**
     * @var array
     */
    private array $property_label;
    /**
     * @var array
     */
    private array $rows;

    /**
     * @param array|null $attributes
     */
    public function __construct(array $attributes = null)
    {
        $this->table = new Table($attributes);
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title): TableWidget
    {
        $this->caption .= "<h1>"._($title)."</h1>";
        return $this;
    }

    /**
     * @param array|null $data
     * @return $this
     */
    public function setData(array $data = null): TableWidget
    {
        if (isset($data['itemListElement'])) {
            $this->rows = $data['itemListElement'];
            $numberOfItems = $data['numberOfItems'];
            $itemListOrder =  $data['itemListOrder'];
        } elseif ($data) {
            $this->rows = $data;
            $numberOfItems = count($data);
            $itemListOrder = "undefined";
        } else {
            $numberOfItems = 0;
            $itemListOrder = "undefined";
        }
        // number of items
        $this->caption .= "<p>".sprintf(_("Showing %s items in %s order"), $numberOfItems, _($itemListOrder))."</p>";
        return $this;
    }

    /**
     * @param array $property_label
     * @return $this
     */
    public function setPropertyLabels(array $property_label): TableWidget
    {
        $this->property_label = $property_label;
        return $this;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function setButtonEdit(string $link): TableWidget
    {
        $this->property_label = [$link=>'Edit'] + $this->property_label;
        return $this;
    }

    /**
     * @param string|null $tableHasPart
     * @return $this
     */
    public function setButtonDelete(string $tableHasPart = null): TableWidget
    {
        $this->tableHasPart = $tableHasPart;

        $this->property_label += ["DELETE"=>'Delete'];

        return $this;
    }

    /**
     * @return array
     */
    public function ready(): array
    {
        // CAPTION
        if ($this->caption) {
            $this->table->caption($this->caption);
        }
        // LABELS
        if ($this->property_label) {
            $this->table->headers($this->property_label);
        }
        // ROWS
        if (empty($this->rows)) {
            $colspan = count($this->property_label);
            $this->table->bodyCell(_("No data were found!"),['colspan'=>$colspan, 'style'=>'text-align: center; font-weight: bold; font-size:120%; color: #fddc2d; '])->closeRow();
        } else {
            foreach ($this->rows as $valueRow) {
                $item = $valueRow['item'] ?? $valueRow;
                $item['id'] = ArrayTool::searchByValue($item['identifier'], 'id', 'value');
                foreach ($this->property_label as $key => $value) {
                    $intersect = array_intersect_key($this->property_label, $item);
                    if (array_key_exists($key, $intersect)) {
                        $this->table->bodyCell($item[$key]);
                    } elseif ($value == 'Edit') {
                        $this->table->bodyCell(self::editButton($key, $item));
                    } elseif ($value == 'Delete') {
                        $this->table->bodyCell(self::deleteButton($item));
                    } else {
                        $this->table->bodyCell($key);
                    }
                }
                $this->table->closeRow();
            }
        }
        // RESPONSE
        return $this->table->ready();
    }

    /**
     * @param $key
     * @param $item
     * @return string
     */
    private static function editButton($key,$item): string
    {
        $link = $key;
        preg_match_all('/\[([^]]+)]*/',$key, $match);
        foreach ($match[1] as $valueMatch) {
            $link = str_replace("[$valueMatch]",urlencode($item[$valueMatch]),$link);
        }
        return "<a href='$link' class='table-itemlist-button table-itemlist-button-edit' title='"._("Edit")."'><span class='material-icons'>edit</span></a>";
    }

    private function deleteButton($item): array
    {
        // VARS
        $type = lcfirst($item['@type']);
        $id = $item['id'];
        $tableHasPart = $item['isPartOf']['@type'] ?? $this->tableHasPart;
        // FORM
        $form = new \Plinct\Web\Element\Form(['class'=>'table-delete-button']);
        $form->action("/admin/$type/erase")->method('post');
        // id
        $form->input('id',$id,'hidden');
        // table has part
        if ($tableHasPart) $form->input('tableHasPart',$tableHasPart,'hidden');
        // button
        $form->submitButtonDelete(null,['class'=>'form-submit-button-delete']);
        // RESPONSE
        return $form->ready();
    }
}