<?php
namespace Plinct\Cms\View\Types\Intangible\Order;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;

abstract class OrderWidget {
    protected $content = [];
    protected static $idOrder;
    protected static $total;
    protected $typeHasPart;
    protected $idHasPart;

    use FormElementsTrait;
    use navbarTrait;

    protected function navbarOrder($value): array {
        $this->typeHasPart = lcfirst($value['@type']);
        $this->idHasPart = ArrayTool::searchByValue($value['identifier'],'id','value');
        $list =  [
            "/admin/$this->typeHasPart/order?id=$this->idHasPart" => _("List all"),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=new" => _("Add new"),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment" => ucfirst(_("payments")),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired" => ucfirst(_("Due dates"))
        ];
        return self::navbar(_("Order"), $list, 4);
    }

    protected function formOrder($case = "new", $value = null, $orderedItem = null): array {
        $content[] = $case == "edit" ? self::input("id", "hidden", self::$idOrder) : null;
        if ($orderedItem) {
            $orderedItemId = ArrayTool::searchByValue($orderedItem['identifier'], "id")['value'];
            $providerId = ArrayTool::searchByValue($orderedItem['provider']['identifier'], "id")['value'];
            $content[] = self::input("orderedItem", "hidden", $orderedItemId);
            $content[] = self::input("orderedItemType", "hidden", lcfirst($orderedItem['@type']));
            $content[] = self::input("seller", "hidden", $providerId);
        }
        // SELLER
        $content[] = self::fieldset(self::chooseType("seller", "organization,person", $value['seller'] ?? null), _("Seller"), [ "style" => "width: 100%;" ]);
        // CUSTOMER
        $content[] = self::fieldset(self::chooseType("customer", "organization,person", $value['customer'] ?? null), _("Customer"), [ "style" => "width: 100%;" ]);
        // ORDER DATE
        $content[] = self::fieldsetWithInput(_("Order date"), "orderDate", isset($value['orderDate']) ? substr($value['orderDate'],0,10) : date("Y-m-d"), [], "date");
        // ORDER STATUS
        $content[] = self::fieldsetWithSelect(_("Order status"), "orderStatus", $value['orderStatus'] ?? null, [
            "OrderProcessing" => _("In processing"),
            "OrderInTransit" => _("In transit"),
            "OrderDelivered" => _("Delivered or performed"),
            "OrderPickupAvailable" => _("Pickup available"),
            "OrderSuspended" => _("Suspended"),
            "OrderCancelled" => _("Cancelled"),
            "OrderProblem" => _("With problem"),
            "OrderReturned" => _("Returned")
        ]);
        // PAYMENT DUE DATE
        $content[] = self::fieldsetWithInput(_("Payment due date"), "paymentDueDate", isset($value['paymentDueDate']) ? substr($value['paymentDueDate'],0,10) : null, [], "date");
        // DISCOUNT
        $content[] = $case == "edit" ? self::fieldsetWithInput(_("Discount"), "discount", $value['discount'] ?? null) : null;
        // TAGS
        $content[] = self::fieldsetWithInput(_("Tags"), "tags", $value['tags'] ?? null, [ "style" => "width: 100%;" ]);
        // SUBMIT
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/order/erase") : null;
        return self::form("/admin/order/$case", $content);
    }

    protected static function getOrderedItems($orderedItem): string {
        if (empty($orderedItem)) {
            return _("Unidentified");
        } else {
            $quantityItems = count($orderedItem);
            $firstItemName = $orderedItem[0]['orderedItem']['name'];
            return $firstItemName . ($quantityItems >1 ? sprintf(_(" more % items."), $quantityItems-1) : null);
        }
    }

    protected function selectPeriodo($numberOfItens, $section): array {
        $content[] = [ "tag" => "form", "attributes" => [ "class" => "noprint", "action" => "/admin/$this->typeHasPart/order", "method" => "get" ], "content" => [
            [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $this->idHasPart ]],
            [ "tag" => "input", "attributes" => [ "name" => "action", "type" => "hidden", "value" => $section ]],
            [ "tag" => "select", "attributes" => [ "onchange" => "submit();", "name" => "period" ], "content" => [
                [ "tag" => "option", "attributes" => [ "value" => "" ], "content" => _("Select by period") ],
                [ "tag" => "option", "attributes" => [ "value" => "past" ], "content" => _("Until today") ],
                [ "tag" => "option", "attributes" => [ "value" => "current_month" ], "content" => _("Until the end of the current month") ],
                [ "tag" => "option", "attributes" => [ "value" => "all" ], "content" => _("View all") ]
            ] ]
        ] ];
        switch (filter_input(INPUT_GET, 'period')) {
            case "current_month":
                $period = _("Until the end of the current month") . " - <b>".DateTime::translateMonth(date('m'))." ".date('Y')."</b>";
                break;
            case "past":
                $period = _("Until today") . " - <b>".DateTime::formatDate();
                break;
            default :
                $period = null;
                break;
        }
        $content[] = [ "tag" => "p", "content" => sprintf(_("Showing %s items %s"), $numberOfItens, $period) ];

        return [ "tag" => "div", "content" => $content ];
    }
}