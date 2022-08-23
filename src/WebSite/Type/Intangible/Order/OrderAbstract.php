<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Order;

use Plinct\Cms\Response\View\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\View;
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
     * @return array
     */
    protected function formOrder(string $case = "new", $value = null): array
    {
        $form = Fragment::form(['class'=>'formPadrao form-order']);
        $form->action("/admin/order/$case")->method('post');
        // hiddens
        if ($case == "edit") $form->input("id", (string) self::$idOrder, "hidden");

        /*if ($orderedItem) {
            $orderedItemId = ArrayTool::searchByValue($orderedItem['identifier'], "id")['value'];
            $providerId = ArrayTool::searchByValue($orderedItem['provider']['identifier'], "id")['value'];
            $form->input("orderedItem", $orderedItemId, "hidden");
            $form->input("orderedItemType", lcfirst($orderedItem['@type']), "hidden");
            $form->input("seller", $providerId, "hidden");
        }*/
        // SELLER
        $form->fieldset(Fragment::form()->chooseType("seller", "organization,person", $value['seller'] ?? null), _("Seller"));
        // CUSTOMER
        $form->fieldset(Fragment::form()->chooseType("customer", "localBusiness,organization,person", $value['customer'] ?? null), _("Customer"));
        // ORDER DATE
        $form->fieldsetWithInput("orderDate", isset($value['orderDate']) ? substr($value['orderDate'],0,10) : date("Y-m-d"), _("Order date"), "date");
        // ORDER STATUS
        $form->fieldsetWithSelect("orderStatus", $value['orderStatus'] ?? null, [
            "OrderProcessing" => _("In processing"),
            "OrderInTransit" => _("In transit"),
            "OrderDelivered" => _("Delivered or performed"),
            "OrderPickupAvailable" => _("Pickup available"),
            "OrderSuspended" => _("Suspended"),
            "OrderCancelled" => _("Cancelled"),
            "OrderProblem" => _("With problem"),
            "OrderReturned" => _("Returned")
        ],_("Order status"));
        // PAYMENT DUE DATE
        $form->fieldsetWithInput("paymentDueDate", isset($value['paymentDueDate']) ? substr($value['paymentDueDate'],0,10) : null, _("Payment due date"), "date");
        // DISCOUNT
        $form->fieldsetWithInput("discount", $value['discount'] ?? null, _("Discount"));
        // TAGS
        $form->fieldsetWithInput("tags", $value['tags'] ?? null, _("Tags"));
        // SUBMIT
        $form->submitButtonSend();
        if ($case == "edit") $form->submitButtonDelete("/admin/order/erase");
        // READY
        return $form->ready();
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
            $string .= " <a href='$uri&period=last2years'>" . sprintf(_("Show last %s years"),'2') . "</a>";
        }

        if ($period != 'last5years') {
            $string .= " <a href='$uri&period=last5years'>" . sprintf(_("Show last %s years"),'5') . "</a>";
        }

        if($period != 'all') {
            $string .= " <a href='$uri&period=all'>" . _("Show all") . "</a>";
        }

        $string .= "</p>";

        return $string;
    }
}
