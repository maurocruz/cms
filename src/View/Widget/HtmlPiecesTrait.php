<?php
namespace Plinct\Cms\View\Widget;

use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

trait HtmlPiecesTrait {
    private static $BUTTON_EDIT = '<img src="https://plinct.com.br/App/static/cms/images/edit4-yellow.svg" width="20" alt="edit">';
    private static $BUTTON_DELETE = "https://plinct.com.br//App/static/cms/images/delete4-red.svg";

    protected static function error($data, $type): array {
        if ($data['code'] == '1146') {
            return [ "tag" => "div", "content" => [
                [ "tag" => "p", "content" => _($data['message']) ],
                [ "tag" => "form", "attributes" => [ "action" => "/admin/$type/createSqlTable", "method" => "post" ], "content" => [
                    [ "tag" => "input", "attributes" => [ "type" => "submit", "value" => _("Do you want to create it?"), "style" => "cursor: pointer;" ] ]
                ]]
            ]];
        } else {
            return [ "tag" => "p", "attributes" => [ "class" => "warning"], "content" => $data['message'] ];
        }
    }

    public static function indexWithSubclass(string $owner, string $type, array $rowsPropeties_and_columnName, array $itemListElement): array {
        $properties = [];
        $uri = $_SERVER['REQUEST_URI'];
        $quantity = count($itemListElement);
        // TABLE
        $table = new Table();
        // CAPTION
        $table->caption(sprintf(_("List of %s from %s (%s)"), lcfirst(_($type)), $owner, $quantity));
        // HEAD
        $table->head(_("Edit"), [ "style" => "width: 50px;"]);
        foreach ($rowsPropeties_and_columnName as $keyColumns => $valueColunmns) {
            $properties[] = $keyColumns;
            $label = is_string($valueColunmns) ? $valueColunmns : $valueColunmns[0];
            $attributes = is_array($valueColunmns) ? $valueColunmns[1] : null;
            $table->head($label, $attributes);
        }
        $table->head(_("Delete"), [ "style" => "width: 50px;"]);
        // BODY
        foreach ($itemListElement as $valueTbody) {
            $item = $valueTbody['item'];
            // EDIT
            $id = ArrayTool::searchByValue($item['identifier'], 'id')['value'];
            $table->bodyCell("<a href='$uri&item=$id'>".self::$BUTTON_EDIT."</a>", [ "style" => "text-align: center" ]);
            // ROWS
            foreach ($properties as $valueProperty) {
                $table->bodyCell(self::getContentBodyCell($item, $valueProperty));
            }
            // DELETE
            $table->bodyCell("<form method='post' action='/admin/$type/erase' style='background-color: transparent; text-align: center;'><input type='hidden' name='id' value='$id'><input type='hidden' name='redirect' value='referrer'/><input src='".self::$BUTTON_DELETE."' type='image' name='submit' style='width: 20px; ' alt='Delete' onclick=\"return confirm('Do you really want to delete this item?')\"/></form>");
            // CLOSE ROW
            $table->closeRow();
        }
        // READY
        return $table->ready();
    }

    private static function getContentBodyCell($item, $valueProperty): ?string {
        // SET PROPERTY
        $property = strstr($valueProperty,":",true) !== false ? strstr($valueProperty,":",true) : $valueProperty;
        // SWITCH
        if (is_array($item[$property]) && isset($item[$property]['name'])) {
            return $item[$property]['name'];
        } elseif (is_array($item[$property])) {
            foreach ($item[$property] as $value) {
                $response[] = $value[$property]['name'];
            }
            return implode(", ", $response);
        } elseif(is_string($item[$property])) {
            return $item[$property];
        }
        return null;
    }
}
