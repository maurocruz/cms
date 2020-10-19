<?php

namespace Plinct\Cms\Views\Html\Piece;

use Plinct\Api\Type\PropertyValue;

trait FormTrait
{
    protected static function div($title, $type, $content)
    {
        $contentOut[] = [ "tag" => "h4", "content" => _($title) ];

        foreach ($content as $value) {
            $contentOut[] = $value;
        }

        return [ "tag" => "div", "attributes" => [ "id" => "$type-form" ], "content" => $contentOut ];
    }

    protected static function divBox($title, $type, $content)
    {
        $contentOut[] = [ "tag" => "h4", "content" => $title ];

        foreach ($content as $value) {
            $contentOut[] = $value;
        }

        return [ "tag" => "div", "attributes" => [ "id" => "$type-form", "class" => "box" ], "content" => $contentOut ];
    }

    protected static function divBoxExpanding($title, $type, $content)
    {
        $id = "$type-form-". mt_rand(111,999);

        $contentOut[] = [ "tag" => "h4", "content" => strip_tags(str_replace("<br>"," ",$title)), "attributes" => [ "class" => "button-dropdown button-dropdown-contracted", "onclick" => "expandBox(this,'$id');" ] ];

        foreach ($content as $value) {
            $contentOut[] = $value;
        }

        return [ "tag" => "div", "attributes" => [ "id" => $id, "class" => "box box-expanding" ], "content" => $contentOut ];
    }

    public static function relationshipOneToOne($tableHasPart, $idHasPart, $propertyName, $tableIsPartOf, $value = null)
    {
        $table = lcfirst($tableIsPartOf);
        if ($value) {
            $id = PropertyValue::extractValue($value['identifier'], "id");

            $content[] = self::input("id", "hidden", $idHasPart);

            $content[] = self::fieldsetWithInput(_($value['@type']) . " <a href=\"/admin/$table/edit/$id\">".("edit this")."</a>", "name", $value['name'], [ "style" => "min-width: 320px; max-width: 600px; width: 100%;" ], "text", [ "disabled" ]);

            $content[] = self::input($propertyName, "hidden", "");

            $content[] = self::submitButtonDelete("/admin/$tableHasPart/edit");

        } else {
            $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => $table, "data-propertyName" => $propertyName, "data-idHasPart" => $idHasPart ] ];
        }

        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "method" => "post", "action" => "/admin/$tableHasPart/edit" ], "content" => $content ];
    }

    public static function relationshipOneToMany($tableHasPart, $idHasPart, $tableIsPartOf, $value = null)
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

    protected static function input($name, $type, $value, $attributes = null)
    {
        $attr = [ "name" => $name, "type" => $type, "value" => $value ];

        $attr2 = $attributes ? array_merge($attr, $attributes) : $attr;

        return [ "tag" => "input", "attributes" => $attr2 ];
    }

    protected static function fieldsetWithInput($legend, $name, $value, $attributes = null, $type = "text", $inputAttributes = null)
    {
        $attr = [ "name" => $name, "type" => $type, "value" => $value ];
        $attributesInput = $inputAttributes ? array_merge($attr, $inputAttributes) : $attr;

        return [ "tag" => "fieldset", "attributes" => $attributes, "content" => [
            [ "tag" => "legend", "content" => _($legend) ],
            [ "tag" => "input", "attributes" => $attributesInput ]
        ]];
    }

    protected static function fieldsetWithTextarea($legend, $name, $value, $height = 150, $attributes = null, $attributes_textarea = null)
    {
        // attributes fieldset
        $h = $height."px";
        $attrFieldset = [ "style" => "width: 100%; min-height: $h;" ];

        $attributesFieldset = $attributes ? array_merge($attrFieldset, $attributes) : $attrFieldset;
        // attributes textarea
        $attrTextarea = [ "name" => $name, "id" => "textarea-$name", "style" => "min-height: calc($h - 50px);" ];
        $attr = $attributes_textarea ? array_merge($attrTextarea, $attributes_textarea) : $attrTextarea;

        return [ "tag" => "fieldset", "attributes" => $attributesFieldset, "content" => [
            [ "tag" => "legend", "content" => _($legend) ],
            [ "tag" => "textarea", "attributes" => $attr, "content" => $value ],
            [ "tag" => "a", "attributes" => [ "href" => "javascript:void();", "onclick" => "expandTextarea('textarea-$name',$height);", "style" => "width: 96%; display: block; font-size: 0.85em;" ], "content" => sprintf(_("Expandir textarea em %s"), $h) ]
        ]];
    }


    protected static function listAll($data, $type, string $title = null, array $row_column = null)
    {
        $caption = $title ? $title : "List of $type";
        $showText = sprintf(_("Show %s items!"), $data['numberOfItems'] ?? 0);

        if (isset($data['errorInfo'])) {
            return self::errorInfo($data['errorInfo'], $type);

        } else {
            $content[] = [ "tag" => "h2", "content" => _($caption) ];
            $content[] = [ "tag" => "p", "content" => $showText ];

            // columns
            $columns = [
                [ "label" => "ID", "property" => "id", "attributes" => [ "style" => "width: 40px;"] ],
                [ "label" => _("Name"), "property" => "name" ]
            ];

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
                foreach ($data['itemListElement'] as $key => $valueItems) {
                    $item = $valueItems['item'];

                    $ID = PropertyValue::extractValue($item['identifier'],"id");

                    $name = '<a href="/admin/'.$type.'/edit/'.$ID.'">'.($item['name'] ?? $item['headline'] ?? "[ND]").'</a>';

                    $rows[] = [ $ID, $name ];

                    if (isset($valueAddRows)) {
                        foreach ($valueAddRows as $valueR) {
                            $array = is_array($item[$valueR]) ? $item[$valueR]['name'] : $item[$valueR];
                            array_push($rows[$key],$array);
                        }
                    }
                }
            }

            $content[] = self::tableItemList($columns, $rows, _($caption));

            return [ "tag" => "div", "content" => $content ];
        }
    }

    protected static function tableItemList(array $columns, array $rows, $caption = null)
    {
        $ordering = filter_input(INPUT_GET, 'ordering');
        $orderingQuery = !$ordering || $ordering === "desc" ? "asc" : "desc";

        foreach ($columns as $valueColumns) {
            $th[] = [ "tag" => "th", "attributes" => $valueColumns['attributes'] ?? null, "content" => '<a href="?orderBy='.$valueColumns['property'].'&ordering='.$orderingQuery.'">'._($valueColumns['label']).'</a>' ];
        }

        $td = null;
        if (count($rows) == 0) { // NO ITENS FOUNDED
            $list[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "colspan" => "5", "style" => "text-align: center; font-weight: bold; font-size: 120%;" ], "content" => _("No items founded!") ]
            ]];

        } else {
            foreach ($rows as $valueRows) {
                foreach ($valueRows as $valueItens ) {
                    $td[] = [ "tag" => "td", "content" => $valueItens['rowText'] ?? $valueItens ];
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

    protected static function submitButtonSend($attributes = null)
    {
        $attr = [ "name" => "submit", "src" => "/App/static/cms/images/ok_64x64.png", "style" => "max-width: 40px; vertical-align: bottom; margin: 6px;", "type" => "image", "alt" => "Enviar", "title" => _("Submit") ];
        $attr2 = $attributes ? array_merge($attr, $attributes) : $attr;
        return [ "tag" => "input", "attributes" => $attr2 ];
    }

    protected static function submitButtonDelete($formaction, $attributes = null)
    {
        $attr = [ "name" => "submit", "src" => "/App/static/cms/images/delete.png", "formaction" => $formaction, "style" => "max-width: 40px; vertical-align: bottom; margin: 6px;", "type" => "image", "alt" => _("Delete data"), "title" => _("Delete data"), "onclick" => "return confirm('".("Are you sure you want to delete this item?")."');" ];
        $attr2 = $attributes ? array_merge($attr, $attributes) : $attr;
        return [ "tag" => "input", "attributes" => $attr2 ];
    }

    protected static function errorInfo($data, $type) {
        if ($data[0] == '42S02') {
            return [ "tag" => "div", "content" => [
                [ "tag" => "p", "content" => _($data[2]) ],
                [ "tag" => "form", "attributes" => [ "action" => "/admin/$type/createSqlTable", "method" => "post" ], "content" => [
                    [ "tag" => "input", "attributes" => [ "type" => "submit", "value" => _("Do you want to install it?") ] ]
                ]]
            ]];
        }

        return null;
    }

}