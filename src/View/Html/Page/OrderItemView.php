<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class OrderItemView implements ViewInterface {
    public static $totalWithoutDiscount;
    public static $totalWithDiscount;

    use FormElementsTrait;

    public function index(array $data): array {
        return [];
    }

    public function new($data = null): array {
        return [];
    }

    public function edit(array $data): array {
        return [];
    }

    public static function getForm($value): array {
        $orderedItem = $value['orderedItem'];
        $itemsLength = (int) 0;
        $Quantities = (int) 0;
        $subtotal = (int) 0;
        $discount = $value['discount'];
        if (is_array($orderedItem)) {
            foreach ($orderedItem as $key => $valueOffer) {
                $item = $valueOffer['orderedItem'];
                $quantity = $valueOffer['orderQuantity'];
                $priceCurrency = $item['offers'][0]['priceCurrency'];
                $unitPrice = $item['offers'][0]['price'];
                $totalPrice = $unitPrice*$quantity;
                $trBody[] = ["tag" => "tr", "attributes" => [ "style" => "color: black; background-color: white;" ], "content" => [
                    [ "tag" => "td", "content" => $key+1 ],
                    [ "tag" => "td", "content" => $item['@type'] ],
                    [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => $item['name'] ],
                    [ "tag" => "td", "content" => $quantity ],
                    [ "tag" => "td", "content" => $priceCurrency." ".number_format($unitPrice,2,",",".") ],
                    [ "tag" => "td", "content" => $priceCurrency." ".number_format($totalPrice,2,",",".") ],
                    [ "tag" => "td", "content" => [
                        [ "tag" => "form", "attributes" => [ "style" => "background-color: inherit; text-align: center;", "method" => "post" ], "content" => [
                            self::input("tableHasPart","hidden","order"),
                            self::input("id","hidden", $valueOffer['idorderItem']),
                            self::submitButtonDelete("/admin/orderItem/erase", [ "style" => "width: 25px;"])
                        ]]
                    ] ]
                ]];
                $subtotal += $totalPrice;
                $Quantities += $quantity;
                $itemsLength += $key+1;
            }
        } else {
            $trBody[] = ["tag" => "tr", "attributes" => [ "style" => "color: black; background-color: white;" ], "content" => [
                [ "tag" => "td", "attributes" => [ "colspan" => "8" ], "content" => _("No items") ]
            ]];
        }
        // SUBTOTAL
        $trBody[] = [ "tag" => "tr", "content" => [
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => "SUBTOTAL" ],
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => $itemsLength." "._("items") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => $Quantities ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => "" ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => number_format($subtotal,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => "" ]
        ]];
        // NEW
        $trBody[] = ["tag" => "tr", "content" => [
            ["tag" => "td", "attributes" => ["colspan" => "8"], "content" => [
                self::formChooseType($value)
            ]]
        ]];
        // TOTAL
        self::$totalWithoutDiscount = $subtotal;
        self::$totalWithDiscount = $subtotal-$discount;
        $trBody[] = [ "tag" => "tr", "content" => [
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => "TOTAL" ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => $itemsLength." "._("items") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => _("Subtotal").": ".number_format($subtotal,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => _("Discount").": ".number_format($discount,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => _("Total").": ".number_format(self::$totalWithDiscount,2,",",".") ]
        ]];
        // TABLE
        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table-form" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 16px;" ], "content" => "#" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => _("Type") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: auto;", "colspan" => "2" ], "content" => _("Item") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 60px;" ], "content" => _("Quantity") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 160px;" ], "content" => _("Unit price") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 160px;" ], "content" => _("Total price") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 50px;" ], "content" => _("Action") ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $trBody ]
        ] ];

        return [ "tag" => "div", "content" => $content ];
    }

    private static function formChooseType($value): array {
         $idSeller = ArrayTool::searchByValue($value['seller']['identifier'], "id")['value'];
         $content[] = self::input("tableHasPart", "hidden", "order");
         $content[] = self::input("orderItemNumber", "hidden", $value['idorder']);
         $content[] = _("Add new").": ";
         $content[] = self::fieldset(self::chooseType("orderedItem", "service,product", null, "name", [ "data-params" => "provider=$idSeller" ]), _("Ordered item"), [ "style" => "width: 70%; "]);
         // QUANTITY
         $content[] = self::fieldsetWithInput(_("Order quantity"), "orderQuantity", "1", [ "style" => "width: 170px;"], "number", [ "min" => "1" ]);
         $content[] = self::submitButtonSend();
         return self::form("/admin/orderItem/new", $content);
     }
}
