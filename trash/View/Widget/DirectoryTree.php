<?php
namespace Plinct\Cms\WebSite\Type\Widget;

use Plinct\Api\Type\PropertyValue;

class DirectoryTree {
    private $data;
    private $numberOfItems;
    private $itemListElement;
    private $view;

    public function __construct($data) {
        $this->data = $data;
        $this->numberOfItems = $data['numberOfItems'];
        $this->itemListElement = $data['itemListElement'];
    }

    public function view() {
        $this->formatData();
        return $this->view;
    }

    private function formatData() {
        $array = [];
        foreach ($this->itemListElement as $value) {
            $url = $value['item']['url'];
            $id = PropertyValue::extractValue($value['item']['identifier'], "id");
            $urlArray = array_filter(explode("/",$url));
            $primaryKey = $urlArray[1];
            $array[$primaryKey][] = [ "parent" => dirname($url), "url" => $url, "id" => $id, "level" => count($urlArray) ];
        }
        ksort($array);
        var_dump($array);
    }
}
