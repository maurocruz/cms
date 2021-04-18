<?php
namespace Plinct\Cms\View\Widget;

use Plinct\Tool\ArrayTool;

trait HtmlPiecesTrait {
    private static $BUTTON_EDIT = '<img src="/App/static/cms/images/edit4-yellow.svg" width="20" alt="edit">';
    private static $BUTTON_DELETE = "/App/static/cms/images/delete4-red.svg";

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
        $listItem = [];
        $uri = $_SERVER['REQUEST_URI'];
        // TABLE
        $table = new Table();
        // CAPTION
        $caption = sprintf(_("List of %s from %s"), lcfirst(_($type)).'s', $owner);
        $table->addCaption($caption);
        // HEAD
        $table->addHead(_("Edit"));
        $table->addHead($rowsPropeties_and_columnName);
        $table->addHead([ "delete" => _("Delete") ]);
        // BODY
        foreach ($itemListElement as $valueTbody) {
            $item = $valueTbody['item'];
            $id = ArrayTool::searchByValue($item['identifier'], 'id')['value'];
            $valueTbody['item'][0] = "<a href='$uri&item=$id'>".self::$BUTTON_EDIT."</a>";
            $valueTbody['item']['id'] = $id;
            $valueTbody['item']['delete'] = "<form method='post' action='/admin/$type/erase' style='background-color: transparent; text-align: center;'><input type='hidden' name='id' value='$id'><input type='hidden' name='redirect' value='referrer'/><input src='".self::$BUTTON_DELETE."' type='image' name='submit' style='width: 20px; ' alt='Delete' onclick=\"return confirm('Do you really want to delete this item?')\"/></form>";
            $listItem[] = $valueTbody['item'];
        }
        $table->addBody($listItem);
        // READY
        return $table->ready();
    }
}
