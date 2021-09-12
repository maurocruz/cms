<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Order;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\StringTool;

abstract class OrderAbstract
{
    /**
     * @var int
     */
    protected static int $idOrder;
    /**
     * @var int
     */
    protected static int $total;
    /**
     * @var string
     */
    protected string $typeHasPart;
    /**
     * @var int
     */
    protected int $idHasPart;

    use FormElementsTrait;

    /**
     * NAVBAR
     *
     * @param $seller
     * @param null $customer
     */
    protected function navbarOrder($seller, $customer = null)
    {
        $this->typeHasPart = lcfirst($seller['@type']);
        $this->idHasPart = (int) ArrayTool::searchByValue($seller['identifier'],'id','value');

        $list =  [
            "/admin/$this->typeHasPart/order?id=$this->idHasPart" => Fragment::icon()->home(),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=new" => Fragment::icon()->plus(),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment" => ucfirst(_("payments")),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired" => ucfirst(_("Due dates"))
        ];

        View::navbar(_("Order"), $list, 4);

        if ($customer) View::navbar($customer,[],5);
    }

    /**
     * FORM TO EDIT OR TO ADD A NEW ORDER
     *
     * @param string $case
     * @param null $value
     * @param null $orderedItem
     * @return array
     */
    protected function formOrder(string $case = "new", $value = null, $orderedItem = null): array
    {
        $content[] = $case == "edit" ? self::input("id", "hidden", (string) self::$idOrder) : null;

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
        $content[] = self::fieldset(self::chooseType("customer", "localBusiness,organization,person", $value['customer'] ?? null), _("Customer"), [ "style" => "width: 100%;" ]);

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
        $content[] = self::fieldsetWithInput(_("Discount"), "discount", $value['discount'] ?? null);
        // TAGS

        $content[] = self::fieldsetWithInput(_("Tags"), "tags", $value['tags'] ?? null, [ "style" => "width: 100%;" ]);

        // SUBMIT
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/order/erase") : null;

        return self::form("/admin/order/$case", $content, ['class'=>'formPadrao form-order']);
    }

    /**
     * @param $numberOfItens
     * @param $section
     * @return array
     */
    protected function selectPeriodo($numberOfItens, $section): array
    {
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

    /**
     * WRITE PARAGRAPH OF THE SELECTED PERIOD
     *
     * @param $period
     * @return string
     */
    protected function periodoParagraph($period): string
    {
        $uri = StringTool::removeDuplicateQueryStrings('period');

        // text
        switch ($period) {
            case '-2 year': $text = 'last 2 years'; break;
            case 'all': $text = 'all'; break;
            default: $text = 'last 5 years'; break;
        }

        $string = "<p class='period-paragraph'>" . sprintf(_('Showing %s'), $text);

        if ($period != 'last2years') {
            $string .= " <a href='$uri&period=last2years'>" . sprintf(_("Show last %s year"),'2') . "</a>";
        }

        if ($period != 'last5years') {
            $string .= " <a href='$uri&period=last5years'>" . sprintf(_("Show last %s year"),'5') . "</a>";
        }

        if($period != 'all') {
            $string .= " <a href='$uri&period=all'>" . _("Show all") . "</a>";
        }

        $string .= "</p>";

        return $string;
    }
}
