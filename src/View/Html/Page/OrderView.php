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
        $this->navbarOrder();
        $this->content['main'][] = self::listAll($data, "order", null, [ "customer" => _("Customer"), "seller" => _("Seller"), "orderDate" => _("Order date") ]);
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
            // order
            $this->content['main'][] = self::divBox(_("Order"), "order", [ self::formOrder("edit", $value) ]);
            // Items
            $this->content['main'][] = self::divBox(_("Ordered items"), "offer", [ self::listItems($value) ]);
            // INVOICE METHODS
            $this->content['main'][] = self::divBox(_("Invoices methods"), "invoice", [ InvoiceView::getForm("order", self::$idOrder, $value) ]);
            // RESUME
            $this->content['main'][] = self::divBox(_("Resume"), "resume", [ self::resume($value) ]);
        }

        return $this->content;
    }

    private static function resume($value): array
    {
        $offer = $value['offer'];
        //var_dump($value['partOfInvoice']);
        return [ "tag" => "table", "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "content" => "Total"]
                ]]
            ] ],
            [ "tag" => "tbody", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "td", "content" => number_format(self::$total,2,",",".") ]
                ]]
            ] ]
        ]];
    }

    private static function listItems($value): array
    {
        $subtotal = (int) 0;
        $discount = $value['discount'];

        $seller = $value['seller'];
        $idSeller = PropertyValue::extractValue($seller['identifier'], "id");

        $offer = $value['offer'];
        if (is_array($offer)) {
            foreach ($offer as $key => $valueOffer) {
                $trBody[] = ["tag" => "tr", "attributes" => [ "style" => "color: black; background-color: white;" ], "content" => [
                    [ "tag" => "td", "content" => $key+1 ],
                    [ "tag" => "td", "content" => $valueOffer['itemOffered']['@type'] ],
                    [ "tag" => "td", "content" => $valueOffer['itemOffered']['name'] ],
                    [ "tag" => "td", "content" => $valueOffer['priceCurrency']." ".number_format($valueOffer['price'],2,",",".") ],
                    [ "tag" => "td", "content" => [
                        [ "tag" => "form", "attributes" => [ "style" => "background-color: inherit; text-align: center;" ], "content" => [
                            self::input("tableHasPart","hidden","order"),
                            self::submitButtonDelete("/admin/offer/erase", [ "style" => "width: 25px;"])
                        ]]
                    ] ]
                ]];
                $subtotal += $valueOffer['price'];
            }
        } else {

            $trBody[] = ["tag" => "tr", "attributes" => [ "style" => "color: black; background-color: white;" ], "content" => [
                [ "tag" => "td", "attributes" => [ "colspan" => "5" ], "content" => _("No items") ]
            ]];
        }
        // SUBTOTAL
        $itemsLength = isset($key) ? ($key+1) : (int) 0;
        $trBody[] = [ "tag" => "tr", "content" => [
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => "SUBTOTAL" ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => $itemsLength." "._("items") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => number_format($subtotal,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => number_format($discount,2,",",".") ]
        ]];
        // NEW
        $trBody[] = ["tag" => "tr", "content" => [
            ["tag" => "td", "attributes" => ["colspan" => "5"], "content" => [
                OfferView::formChooseType("order", self::$idOrder, $idSeller, _("New item"))
            ]]
        ]];
        // TOTAL
        self::$total = $subtotal-$discount;
        $trBody[] = [ "tag" => "tr", "content" => [
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => "TOTAL" ],
            [ "tag" => "td", "attributes" => [ "colspan" => "1" ], "content" => $itemsLength." "._("items") ],
            [ "tag" => "td", "attributes" => [ "colspan" => "2" ], "content" => number_format(self::$total,2,",",".") ]
        ]];

        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table-form" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "th", "attributes" => [ "style" => "width: 16px;" ], "content" => "#" ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => _("Type") ],
                [ "tag" => "th", "attributes" => [ "style" => "width: auto;" ], "content" => _("Item") ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 160px;" ], "content" => _("Price") ],
                [ "tag" => "th", "attributes" => [ "style" => "width: 50px;" ], "content" => _("Action") ]
            ] ],
            [ "tag" => "tbody", "content" => $trBody ]
        ] ];

        return [ "tag" => "div", "content" => $content ];
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
        $content[] = self::fieldset(self::chooseType("customer", "organization,person", $value['customer'], "name", [ "style" => "display: flex;"]), _("Customer"), [ "style" => "width: 100%;" ]);
        // SELLER
        $content[] = self::fieldset(self::chooseType("seller", "organization,person", $value['seller'], "name", [ "style" => "display: flex;"]), _("Seller"), [ "style" => "width: 100%;" ]);
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

        $content[] = self::submitButtonSend();

        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/order/erase") : null;

        return self::form("/admin/order/$case", $content);
    }
}