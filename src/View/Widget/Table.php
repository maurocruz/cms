<?php
namespace Plinct\Cms\View\Widget;

class Table {
    private $table = [ "tag" => "table", "attributes" => [ "class" => "table"] ];
    private $caption = [ "tag" => "caption" ];
    private $thead = [ "tag" => "thead" ];
    private $tbody = [ "tag" => "tbody" ];
    private $tfoot = [ "tag" => "tfoot" ];
    private $rowsColumns;
    private $itemListElement;

    public function setCaption($caption): Table {
        $this->caption['content'][] = $caption;
        return $this;
    }

    public function setRowsColumns(array $rowsColumns) {
        $this->rowsColumns = $rowsColumns;
    }

    public function setItemListElement(array $itemListElement) {
        $this->itemListElement = $itemListElement;
    }

    private function addHead() {
        $th = null;
        foreach ($this->rowsColumns as $columnName) {
            $th[] = [ "tag" => "th", "content" => "<a href='{$_SERVER['REQUEST_URI']}&orderBy=$columnName'>"._(ucfirst($columnName))."</a>" ];
        }
        $this->thead['content'][] = [ "tag" => "tr", "content" => $th ];
    }

    private function addBody() {
        $td = null;
        foreach ($this->itemListElement as $valueTbody) {
            $item = $valueTbody['item'];
            foreach ($this->rowsColumns as $rowsProperty => $columnName) {
                $td[] = isset($item[$rowsProperty]) ? [ "tag" => "td", "content" => $item[$rowsProperty] ] : null;
            }
            $this->tbody['content'][] = [ "tag" => "tr", "content" => $td ];
        }
    }

    private function addFoot() {
    }

    public function ready(): array {
        $this->table['content'][] = $this->caption;
        if ($this->rowsColumns) {
            $this->addHead();
            $this->table['content'][] = $this->thead;
        }
        if ($this->itemListElement) {
            $this->addBody();
            $this->table['content'][] = $this->tbody;
        }
        $this->addFoot();
        $this->table['content'][] = $this->tfoot;
        return $this->table;
    }
}