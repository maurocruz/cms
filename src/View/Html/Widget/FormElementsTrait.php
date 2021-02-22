<?php

namespace Plinct\Cms\View\Html\Widget;

use Plinct\Api\Type\PropertyValue;
use Plinct\Web\Widget\FormTrait;

trait FormElementsTrait
{
    use FormTrait;

    /**
     * Creates a type selection form and chooses the type from a pop-up in an input form
     * @param string $property
     * @param string|array $typesForChoose
     * @param array|bool $value
     * @param string $nameLike
     * @param array|null $attributes
     * @return array
     */
    public static function chooseType(string $property, $typesForChoose, $value, string $nameLike = "name", array $attributes = []) : array
    {
        $attributes2['class'] = "choose-type";
        $attributes2['data-property'] = $property;
        $attributes2['data-types'] = is_array($typesForChoose) ? implode(",",$typesForChoose) : $typesForChoose;
        $attributes2['data-like'] = $nameLike;
        $attributes2['data-currentType'] = $value['@type'];
        $attributes2['data-currentName'] = $value['name'];
        $attributes2['data-currentId'] = PropertyValue::extractValue($value['identifier'], "id");
        $widthAttr = "display: flex; min-height: 23px;";
        $attributes2['style'] = array_key_exists('style', $attributes) ? $widthAttr." ".$attributes['style'] : $widthAttr;
        unset($attributes['style']);
        $attributes3 = $attributes ? array_merge($attributes2, $attributes) : $attributes2;
        return [ "tag" => "div", "attributes" => $attributes3 ];
    }

    protected static function datalist(string $id, array $array): string
    {
        $content = null;
        foreach ($array as $value) {
            $content .= "<option value='$value'>";
        }
        return "<datalist id='$id'>$content</datalist>";
    }

    protected static function div($title, $type, $content): array
    {
        $contentOut[] = [ "tag" => "h4", "content" => _($title) ];
        foreach ($content as $value) {
            $contentOut[] = $value;
        }
        return [ "tag" => "div", "attributes" => [ "id" => "$type-form" ], "content" => $contentOut ];
    }

    protected static function divBox2($title, $content): array {
        $contentOut[] = [ "tag" => "h4", "content" => $title ];
        foreach ($content as $value) {
            $contentOut[] = $value;
        }
        return [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $contentOut ];
    }

    protected static function divBox($title, $type, $content): array
    {
        $id = "$type-form-". mt_rand(111,999);
        $contentOut[] = [ "tag" => "h4", "content" => $title ];
        foreach ($content as $value) {
            $contentOut[] = $value;
        }
        return [ "tag" => "div", "attributes" => [ "id" => $id, "class" => "box" ], "content" => $contentOut ];
    }

    protected static function divBoxExpanding($title, $type, $content): array
    {
        $id = "$type-form-". mt_rand(111,999);
        $contentOut[] = [ "tag" => "h4", "content" => $title, "attributes" => [ "class" => "button-dropdown button-dropdown-contracted", "onclick" => "expandBox(this,'$id');" ] ];
        foreach ($content as $value) {
            $contentOut[] = $value;
        }
        return [ "tag" => "div", "attributes" => [ "id" => $id, "class" => "box box-expanding" ], "content" => $contentOut ];
    }

    public static function relationshipOneToOne($tableHasPart, $idHasPart, $propertyName, $tableIsPartOf, $value = null): array
    {
        $table = lcfirst($tableIsPartOf);
        if ($value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");
            $content[] = self::input("id", "hidden", $idHasPart);
            $content[] = self::fieldsetWithInput(_($value['@type']) . " <a href=\"/admin/$table/edit/$id\">"._("Edit")."</a>", "name", $value['name'], [ "style" => "min-width: 320px; max-width: 600px; width: 100%;" ], "text", [ "disabled" ]);
            $content[] = self::input($propertyName, "hidden", "");
            $content[] = self::submitButtonDelete("/admin/$tableHasPart/edit");
        } else {
            $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => $table, "data-propertyName" => $propertyName, "data-idHasPart" => $idHasPart ] ];
        }
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "method" => "post", "action" => "/admin/$tableHasPart/edit" ], "content" => $content ];
    }

    public static function relationshipOneToMany($tableHasPart, $idHasPart, $tableIsPartOf, $value = null): array
    {
        if ($value) {
            foreach ($value as $person) {
                $id = PropertyValue::extractValue($person['identifier'], "id");
                $table = lcfirst($tableIsPartOf);
                $content[] = self::input("tableHasPart", "hidden", $tableHasPart);
                $content[] = self::input("idHasPart", "hidden", $idHasPart);
                $content[] = self::input("idIsPartOf", "hidden", $id);
                $content[] = self::fieldsetWithInput(_($person['@type']) . " <a href=\"/admin/$table/edit/$id\">".("edit this")."</a>", "name", $person['name'], ["style" => "min-width: 320px; max-width: 600px; width: 100%;"], "text", ["disabled"]);
                $content[] = self::submitButtonDelete("/admin/$table/erase");
                $return[] = ["tag" => "form", "attributes" => ["class" => "formPadrao", "method" => "post", "action" => "/admin/$table/edit"], "content" => $content];
                unset($content);
            }
        }
        $content[] = self::input("tableHasPart", "hidden", $tableHasPart);
        $content[] = self::input("idHasPart", "hidden", $idHasPart);
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => lcfirst($tableIsPartOf), "data-idHasPart" => $idHasPart  ] ];
        $return[] = ["tag" => "form", "attributes" => ["class" => "formPadrao", "method" => "post", "action" => "/admin/" . lcfirst($tableIsPartOf) . "/new"], "content" => $content];
        return $return;
    }

    public static function listAll($data, $type, string $title = null, array $row_column = null): ?array
    {
        $caption = $title ? $title : "List of $type";
        $showText = sprintf(_("Showing %s from %s items."), count($data['itemListElement']), $data['numberOfItems']);
        if (isset($data['error'])) {
            return self::errorInfo($data['error'], $type);
        } else {
            $itemListElement = $data['itemListElement'];
            $content[] = [ "tag" => "h2", "content" => _($caption) ];
            $content[] = [ "tag" => "p", "content" => $showText ];
            // columns
            $columns[] = [ "label" => "Action", "property" => "action", "attributes" => [ "style" => "width: 40px;"] ];
            $columns[] = [ "label" => "ID", "property" => "id$type", "attributes" => [ "style" => "width: 40px;"] ];
            if (isset($itemListElement[0]['item']['name'])) {
                $columns[] = ["label" => "Name", "property" => "name"];
            }
            if ($row_column) {
                foreach ($row_column as $keyCR => $valueCR) {
                    $columns[] = [ "label" => $valueCR, "property" => $keyCR ];
                    $valueAddRows[] = $keyCR;
                }
            }
            // rows
            if (!isset($data['numberOfItems']) || $data['numberOfItems'] == 0) {
                $rows = [];
            } else {
                foreach ($itemListElement as $key => $valueItems) {
                    $item = $valueItems['item'];
                    $rowItem[] = PropertyValue::extractValue($item['identifier'],"id");
                    if (isset($item['name'])) {
                        $rowItem[] = $item['name'];
                    }
                    if (isset($valueAddRows)) {
                        foreach ($valueAddRows as $valueR) {
                            $property = strstr($valueR,":",true) != false ? strstr($valueR,":",true) : $valueR;
                            $index = substr(strstr($valueR,":"),1) != false ? substr(strstr($valueR,":"),1) : "name";
                            if (is_string($item[$property])) {
                                $rowItem[] = $item[$property];
                            } elseif (isset($item[$property]) && is_array($item[$property])) {
                                $rowItem[] = isset($item[$property][$index]) ? $item[$property][$index] : (isset($item[$property]) ? $item[$property] : null);
                            } else {
                                $rowItem[] = "No content";
                            }
                        }
                    }
                    $rows[] = $rowItem;
                    unset($rowItem);
                }
            }
            $content[] = self::tableItemList($type, $columns, $rows);
            return [ "tag" => "div", "content" => $content ];
        }
    }

    protected static function tableItemList(string $type, array $columns, array $rows): array
    {
        $ordering = filter_input(INPUT_GET, 'ordering');
        $orderingQuery = !$ordering || $ordering === "desc" ? "asc" : "desc";
        // LABEL COLUMNS
        foreach ($columns as $valueColumns) {
            $property = $valueColumns['property'];
            $label = $valueColumns['label'];
            //$content = $valueColumns['label'] != "Action" ? '<a href="?orderBy='.$valueColumns['property'].'&ordering='.$orderingQuery.'">'._($valueColumns['label']).'</a>' :$valueColumns['label'];
            $content = $valueColumns['label'] != "Action" ? sprintf('<a href="?orderBy=%s&ordering=%s">%s</a>', $property, $orderingQuery, _($label)) : _($label);
            $th[] = [ "tag" => "th", "attributes" => $valueColumns['attributes'] ?? null, "content" => $content ];
        }
        $td = null;
        if (count($rows) == 0) { // NO ITENS FOUNDED
            $list[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "colspan" => count($columns), "style" => "text-align: center; font-weight: bold; font-size: 120%;" ], "content" => _("No items founded!") ]
            ]];
        } else {
            foreach ($rows as $valueRows) {
                // actions
                $td[] = [ "tag" => "td", "content" => '<a href="/admin/'.$type.'/edit/'.$valueRows[0].'">'._("Edit").'</a>' ];
                foreach ($valueRows as $valueItens ) {
                    if ($valueItens !== '' && !isset($valueItens['rowText'])) {
                        $contentTd = _($valueItens);
                    } elseif(isset($valueItens['rowText']) && $valueItens['rowText'] !== ''){
                        $contentTd = _($valueItens['rowText']);
                    } else {
                        $contentTd = $valueItens;
                    }
                    $td[] = [ "tag" => "td", "content" => $contentTd ];
                }
                $list[] = [ "tag" => "tr", "content" => $td ];
                unset($td);
            }
        }
        return [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => $th ]
            ]],
            [ "tag" => "tbody", "content" => $list ]
        ]];
    }

    protected static function errorInfo($data, $type): ?array
    {
        if ($data['code'] == '42S02' || $data['code'] == '1146') {
            return [ "tag" => "div", "content" => [
                [ "tag" => "p", "content" => _($data['message']) ],
                [ "tag" => "form", "attributes" => [ "action" => "/admin/$type/createSqlTable", "method" => "post" ], "content" => [
                    [ "tag" => "input", "attributes" => [ "type" => "submit", "value" => _("Do you want to install it?") ] ]
                ]]
            ]];
        }
        return null;
    }
}