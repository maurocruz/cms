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

    use FormElementsTrait;

    protected function navbarOrder($title = null, $list = null, $level = 2) {
        $title = $title ?? _("Order");
        $list = $list ?? [
                "/admin/order" => _("List all"),
                "/admin/order/new" => _("Add new"),
                "/admin/order/payment" => ucfirst(_("payments")),
                "/admin/order/expired" => ucfirst(_("Due dates"))
            ];
        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level);
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
        $submitAttributes = $case == "edit" ? [ "onclick" => "return setHistory(this.parentNode);" ] : null;
        $content[] = self::submitButtonSend($submitAttributes);
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

    protected static function selectPeriodo($numberOfItens, $section): array {
        $content[] = [ "tag" => "form", "attributes" => [ "class" => "noprint", "action" => "/admin/order/$section", "method" => "get" ], "content" => [
            [ "tag" => "select", "attributes" => [ "onchange" => "submit();", "name" => "period" ], "content" => [
                [ "tag" => "option", "attributes" => [ "value" => "" ], "content" => "Selecionar por período" ],
                [ "tag" => "option", "attributes" => [ "value" => "past" ], "content" => "Até hoje" ],
                [ "tag" => "option", "attributes" => [ "value" => "current_month" ], "content" => "Até o fim do mês corrente" ],
                [ "tag" => "option", "attributes" => [ "value" => "all" ], "content" => "Todos" ]
            ] ]
        ] ];
        switch (filter_input(INPUT_GET, 'period')) {
            case "current_month":
                $period = "até o mês corrente - <b>".DateTime::translateMonth(date('m'))." ".date('Y')."</b>";
                break;
            case "past":
                $period = "até hoje - <b>".DateTime::formatDate();
                break;
            default :
                $period = null;
                break;
        }
        $content[] = [ "tag" => "p", "content" => sprintf(_("Showing %s items %s"), $numberOfItens, $period) ];

        return [ "tag" => "div", "content" => $content ];
    }
}