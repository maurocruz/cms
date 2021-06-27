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

    public static function indexWithSubclass(array $value, string $type, array $rowsPropeties_and_columnName, array $itemListElement = null): array {
        $name = $value['name'];
        $tableHasPart = lcfirst($value['@type']);
        $properties = [];
        $parseUrl = parse_url($_SERVER['REQUEST_URI']);
        $path = $parseUrl['path'];
        parse_str($parseUrl['query'], $queryArray);
        $id = $queryArray['id'];
        $rows = is_array($itemListElement) ? count($itemListElement) : 0;
        $columns = count($rowsPropeties_and_columnName) + 2;
        // TABLE
        $table = new Table();
        // CAPTION
        $table->caption(sprintf(_("List of %s from %s (%s)"), lcfirst(_($type)), $name, $rows));
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
        if ($rows == 0) {
            $table->bodyCell(_("No items founded!"), [ "colspan" => $columns, "style" => "text-align:center; font-size: 120%; font-weight: bold; color: yellow;" ]);
            $table->closeRow();
        } else {
            foreach ($itemListElement as $valueTbody) {
                $item = $valueTbody['item'];
                // EDIT
                $idItem = ArrayTool::searchByValue($item['identifier'], 'id')['value'];
                $table->bodyCell("<a href='$path?id=$id&item=$idItem'>" . self::$BUTTON_EDIT . "</a>", ["style" => "text-align: center"]);
                // ROWS
                foreach ($properties as $valueProperty) {
                    $table->bodyCell(self::getContentBodyCell($item, $valueProperty));
                }
                // DELETE
                $table->bodyCell("<form method='post' action='/admin/$type/erase' style='background-color: transparent; text-align: center;'><input type='hidden' name='id' value='$id'><input type='hidden' name='tableHasPart' value='$tableHasPart'/><input src='" . self::$BUTTON_DELETE . "' type='image' name='submit' style='width: 20px; ' alt='Delete' onclick=\"return confirm('Do you really want to delete this item?')\"/></form>");
                // CLOSE ROW
                $table->closeRow();
            }
        }
        // READY
        return $table->ready();
    }

    private static function getContentBodyCell($item, $valueProperty): ?string {
        $response = null;
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

    /**
     * TABLE OF ITEM LISTING ON INDEX PAGES
     * @param $data
     * @param $type
     * @param string|null $title
     * @param array|null $row_column
     * @return array|null
     */
    public static function listAll($data, $type, string $title = null, array $row_column = null): ?array {
        $caption = $title ?? "List of $type";
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
            $rows = [];
            if (isset($data['numberOfItems']) || $data['numberOfItems'] !== 0) {
                foreach ($itemListElement as $valueItems) {
                    $item = $valueItems['item'];
                    $rowItem[] = ArrayTool::searchByValue($item['identifier'],"id")['value'];
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
                                $rowItem[] = $item[$property][$index] ?? ($item[$property] ?? null);
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

    private static function tableItemList(string $type, array $columns, array $rows): array {
        $ordering = filter_input(INPUT_GET, 'ordering');
        $orderingQuery = !$ordering || $ordering === "desc" ? "asc" : "desc";
        $th = null;
        $td = null;
        $list = null;
        // LABEL COLUMNS
        foreach ($columns as $valueColumns) {
            $property = $valueColumns['property'];
            $label = $valueColumns['label'];
            $content = $valueColumns['label'] != "Action" ? sprintf('<a href="?orderBy=%s&ordering=%s">%s</a>', $property, $orderingQuery, _($label)) : _($label);
            $th[] = [ "tag" => "th", "attributes" => $valueColumns['attributes'] ?? null, "content" => $content ];
        }
        if (count($rows) == 0) { // NO ITENS FOUNDED
            $list[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "colspan" => count($columns), "style" => "text-align: center; font-weight: bold; font-size: 120%;" ], "content" => _("No items founded!") ]
            ]];
        } else {
            foreach ($rows as $valueRows) {
                // actions
                $td[] = [ "tag" => "td", "content" => sprintf('<a href="/admin/%s/edit/%s" class="table-itemlist-button table-itemlist-button-edit"  title="%s""><span class="material-icons">edit</span></a>', $type, $valueRows[0], _("Edit")) ];
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
        return [ "tag" => "table", "attributes" => [ "class" => "table table-itemlist" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => $th ]
            ]],
            [ "tag" => "tbody", "content" => $list ]
        ]];
    }
}
