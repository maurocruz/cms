<?php
namespace Plinct\Cms\View\Widget;

use Plinct\Tool\ArrayTool;

trait HtmlPiecesTrait {

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
        $itemList = [];
        $imageEdit = '<img src="/App/static/cms/images/edit3.svg" width="20" alt="edit">';
        $imageDelete = '<img src="/App/static/cms/images/delete3.svg" width="20" alt="delete">';
        $uri = $_SERVER['REQUEST_URI'];
        // TABLE
        $table = new Table();
        // CAPTION
        $caption = sprintf(_("List of %s from %s"), _($type), $owner);
        $table->setCaption($caption);
        // HEAD
        array_unshift($rowsPropeties_and_columnName,_("Edit"));
        array_push($rowsPropeties_and_columnName, _("Delete"));
        $table->setRowsColumns($rowsPropeties_and_columnName);
        // BODY
        foreach ($itemListElement as $valueTbody) {
            $item = $valueTbody['item'];
            $id = ArrayTool::searchByValue($item['identifier'], 'id')['value'];
            $valueTbody['item'][0] = "<a href='$uri&item=$id'>$imageEdit</a>";
            $valueTbody['item'][1] = "<a href='$uri&item=$id&action=deleteItem' onclick=\"return confirm('Do you really want to delete this item?')\">$imageDelete</a>";
            $itemList[] = $valueTbody;
        }
        $table->setItemListElement($itemList);
        // READY
        return $table->ready();
    }
}
