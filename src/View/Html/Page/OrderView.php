<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;

class OrderView implements ViewInterface
{
    private $content = [];

    private static $idOrder;

    private static $total;

    use FormElementsTrait;

    private function navbarOrder($title = null, $list = null, $level = 2)
    {
        $title = $title ?? _("Order");
        $list = $list ?? [ "/admin/order" => _("List all"), "/admin/order/new" => _("Add new") ];
        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level);
    }

    public function index(array $data): array
    {
        //var_dump($data['itemListElement'][0]['item']);
        $this->navbarOrder();
        // SEARCH
        $this->content['main'][] = self::search("","search");
        // LIST
        $this->content['main'][] = self::listAll($data, "order", null, [ "customer" => _("Customer"), "seller" => _("Seller"), "orderDate" => _("Order date"), "orderStatus" => _("Order status") ]);
        return $this->content;
    }

    public function new($data = null): array
    {
        $this->navbarOrder();

        $orderedItem = $data['orderedItem'] ?? null;

        if ($orderedItem) {
            $title = sprintf(_("New order for %s"), $orderedItem['@type']." \"".$orderedItem['name']."\"");
        } else {
            $title = _("New order");
        }

        $this->navbarOrder($title, [], 3);

        // order
        $this->content['main'][] = self::divBox(_("New order"), "order", [ self::formOrder("new", null, $orderedItem) ]);

        return $this->content;
    }

    public function edit(array $data): array
    {
        $this->navbarOrder();

        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            $value = $data[0];
            self::$idOrder = PropertyValue::extractValue($value['identifier'], "id");
            $title = sprintf(_("Order from '%s' for '%s'"), $value['customer']['name'], $value['seller']['name']);
            $this->navbarOrder($title, [], 3);

            // ORDER
            $this->content['main'][] = self::divBox(_("Order"), "order", [ self::formOrder("edit", $value) ]);
            // ORDERED ITEMS
            $this->content['main'][] = self::divBox(_("Ordered items"), "offer", [ OrderItemView::getForm($value) ]);
            // INVOICES
            $this->content['main'][] = self::divBox(_("Invoices"), "invoice", [ InvoiceView::getForm("order", self::$idOrder, $value) ]);
            // HISTORY
            $this->content['main'][] = (new HistoryView())->view($value['history']);
        }

        return $this->content;
    }

    private function formOrder($case = "new", $value = null, $orderedItem = null): array
    {
        $content[] = $case == "edit" ? self::input("id", "hidden", self::$idOrder) : null;

        if ($orderedItem) {
            $orderedItemId = PropertyValue::extractValue($orderedItem['identifier'], "id");
            $providerId = PropertyValue::extractValue($orderedItem['provider']['identifier'], "id");
            $content[] = self::input("orderedItem", "hidden", $orderedItemId);
            $content[] = self::input("orderedItemType", "hidden", lcfirst($orderedItem['@type']));
            $content[] = self::input("seller", "hidden", $providerId);
        }

        // CUSTOMER
        $content[] = self::fieldset(self::chooseType("customer", "localBusiness,organization,person", $value['customer']), _("Customer"), [ "style" => "width: 100%;" ]);
        // SELLER
        $content[] = self::fieldset(self::chooseType("seller", "organization,person", $value['seller']), _("Seller"), [ "style" => "width: 100%;" ]);
        // ORDER DATE
        $content[] = self::fieldsetWithInput(_("Order date"), "orderDate", $value['orderDate'] ? substr($value['orderDate'],0,10) : date("Y-m-d"), [], "date");
        // ORDER STATUS
        $content[] = self::fieldsetWithSelect(_("Order status"), "orderStatus", $value['orderStatus'], [
            "OrderCancelled" => _("Order Cancelled"),
            "OrderDelivered" => _("Order delivered"),
            "OrderInTransit" => _("Order in transit"),
            "OrderISuspended" => _("Order suspended"),
            "OrderPaymentDue" => _("Order payment due"),
            "OrderPickupAvailable" => _("Order pickup available"),
            "OrderProblem" => _("Order problem"),
            "OrderProcessing" => _("Order processing"),
            "OrderReturned" => _("Order returned")
        ]);
        // PAYMENT DUE DATE
        $content[] = self::fieldsetWithInput(_("Payment due date"), "paymentDueDate", substr($value['paymentDueDate'],0,10), [], "date");
        // DISCOUNT
        $content[] = self::fieldsetWithInput(_("Discount"), "discount", $value['discount']);

        $submitAttributes = $case == "edit" ? [ "onclick" => "return setHistory(this.parentNode);" ] : null;
        $content[] = self::submitButtonSend($submitAttributes);

        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/order/erase") : null;

        return self::form("/admin/order/$case", $content);
    }
}