<?php
namespace Plinct\Cms\View\Widget;

use Plinct\Cms\Server\SchemaorgData;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Widget\FormTrait;

trait FormElementsTrait {

    use HtmlPiecesTrait;
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
    public static function chooseType(string $property, $typesForChoose, $value, string $nameLike = "name", array $attributes = []) : array {
        $attributes2['class'] = "choose-type";
        $attributes2['data-property'] = $property;
        $attributes2['data-types'] = is_array($typesForChoose) ? implode(",",$typesForChoose) : $typesForChoose;
        $attributes2['data-like'] = $nameLike;
        $attributes2['data-currentType'] = $value['@type'] ?? null;
        $attributes2['data-currentName'] = $value['name'] ?? null;
        $attributes2['data-currentId'] = isset($value['identifier']) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $widthAttr = "display: flex; min-height: 23px;";
        $attributes2['style'] = array_key_exists('style', $attributes) ? $widthAttr." ".$attributes['style'] : $widthAttr;
        unset($attributes['style']);
        $attributes3 = $attributes ? array_merge($attributes2, $attributes) : $attributes2;
        return [ "tag" => "div", "attributes" => $attributes3 ];
    }

    protected static function datalist(string $id, array $array): string {
        $content = null;
        foreach ($array as $value) {
            $content .= "<option value='$value'>";
        }
        return "<datalist id='$id'>$content</datalist>";
    }

    protected static function div($title, $type, $content): array {
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

    protected static function divBox($title, $type, $content): array {
        $id = "$type-form-". mt_rand(111,999);
        $contentOut[] = [ "tag" => "h4", "content" => $title ];
        foreach ($content as $value) {
            $contentOut[] = $value;
        }
        return [ "tag" => "div", "attributes" => [ "id" => $id, "class" => "box" ], "content" => $contentOut ];
    }

    protected static function divBoxExpanding($title, $type, $content): array {
        $id = "$type-form-". mt_rand(111,999);
        $contentOut[] = [ "tag" => "h4", "content" => $title, "attributes" => [ "class" => "button-dropdown button-dropdown-contracted", "onclick" => "expandBox(this,'$id');" ] ];
        foreach ($content as $value) {
            $contentOut[] = $value;
        }
        return [ "tag" => "div", "attributes" => [ "id" => $id, "class" => "box box-expanding" ], "content" => $contentOut ];
    }

    public static function relationshipOneToOne($tableHasPart, $idHasPart, $propertyName, $tableIsPartOf, $value = null): array {
        $table = lcfirst($tableIsPartOf);
        if ($value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $content[] = self::input("id", "hidden", $idHasPart);
            $content[] = self::fieldsetWithInput(_($value['@type']) . " <a href=\"/admin/$table/edit/$id\">"._("Edit")."</a>", "name", $value['name'], null, "text", [ "disabled" ]);
            $content[] = self::input($propertyName, "hidden", "");
            $content[] = self::submitButtonDelete("/admin/$tableHasPart/edit");
        } else {
            $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => $table, "data-propertyName" => $propertyName, "data-idHasPart" => $idHasPart ] ];
        }
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao form-relationship", "method" => "post", "action" => "/admin/$tableHasPart/edit" ], "content" => $content ];
    }

    public static function relationshipOneToMany($tableHasPart, $idHasPart, $tableIsPartOf, $value = null): array {
        if ($value) {
            foreach ($value as $item) {
                $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
                $table = lcfirst($tableIsPartOf);
                $content[] = self::input("tableHasPart", "hidden", $tableHasPart);
                $content[] = self::input("idHasPart", "hidden", $idHasPart);
                $content[] = self::input("idIsPartOf", "hidden", $id);
                $content[] = self::fieldsetWithInput(_($item['@type']) . " <a href=\"/admin/$table/edit/$id\">".("edit this")."</a>", "name", $item['name'], null, "text", ["disabled"]);
                $content[] = self::submitButtonDelete("/admin/$table/erase");
                $return[] = ["tag" => "form", "attributes" => ["class" => "formPadrao", "method" => "post", "action" => "/admin/$table/edit"], "content" => $content];
                unset($content);
            }
        }
        $content[] = self::input("tableHasPart", "hidden", $tableHasPart);
        $content[] = self::input("idHasPart", "hidden", $idHasPart);
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => lcfirst($tableIsPartOf), "data-idHasPart" => $idHasPart  ] ];
        $return[] = ["tag" => "form", "attributes" => ["class" => "formPadrao form-relationship", "method" => "post", "action" => "/admin/" . lcfirst($tableIsPartOf) . "/new"], "content" => $content];
        return $return;
    }

    /*protected static function errorInfo($data, $type): ?array {
        if ($data['code'] == '42S02' || $data['code'] == '1146') {
            return [ "tag" => "div", "content" => [
                [ "tag" => "p", "content" => _($data['message']) ],
                [ "tag" => "form", "attributes" => [ "action" => "/admin/$type/createSqlTable", "method" => "post" ], "content" => [
                    [ "tag" => "input", "attributes" => [ "type" => "submit", "value" => _("Do you want to install it?") ] ]
                ]]
            ]];
        }
        return null;
    }*/

    protected static function additionalTypeInput($typeSelected, $additionalTypeValue, $attributes = null, $includeType = true): array {
        $datalist = null;
        $newAdditionalTypes = null;
        $additionalTypes = (new SchemaorgData())->getSchemaByTypeSelected($typeSelected,$includeType);
        // translate additional types
        foreach ($additionalTypes as $value) {
            $newAdditionalTypes[] = self::translateAdditionalTypes($value);
        }
        // additionalType
        foreach ($newAdditionalTypes as $valueAddTypes) {
            $datalist[] = [ "tag" => "option", "attributes" => [ "value" => $valueAddTypes ] ];
        }
        return [ "tag" => "fieldset", "attributes" => $attributes, "content" => [
            [ "tag" => "legend", "content" => _("Additional type") ],
            [ "tag" => "input", "attributes" => [ "name" => "additionalType", "type" => "text", "value" => self::translateAdditionalTypes($additionalTypeValue), "list" => "additionalType", "autocomplete" => "off" ] ],
            [ "tag" => "datalist", "attributes" => [ "id" => "additionalType"], "content" => $datalist ]
        ] ];
    }

    private static function translateAdditionalTypes($additionalTypes): ?string {
        if ($additionalTypes) {
            $newArray = null;
            $explode = explode(",", $additionalTypes);
            foreach ($explode as $valueItem) {
                $newArray[] = _($valueItem);
            }
            return implode(" -> ", $newArray);
        }
        return null;
    }
}
