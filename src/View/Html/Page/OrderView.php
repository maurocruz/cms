<?php


namespace Plinct\Cms\View\Html\Page;


use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;

class OrderView implements ViewInterface
{
    private $content = [];

    private $id;

    use FormElementsTrait;

    private function navbarOrder($title = null)
    {
        $list = [
            "/admin/order" => _("List all")
        ];

        $this->content['navbar'][] = navbarTrait::navbar(_("Order"), $list);

        if ($title) {
            $this->content['navbar'][] = navbarTrait::navbar($title, [], 3);
        }
    }

    public function index(array $data): array
    {
        $this->navbarOrder();

        $this->content['main'][] = self::listAll($data, "order");

        return $this->content;
    }

    public function edit(array $data): array
    {
        return [];
    }

    public function new($data = null): array
    {
        $orderedItem = $data['orderedItem'] ?? null;
        if ($orderedItem) {
            $title = sprintf(_("New order for %s"), $orderedItem['@type']." \"".$orderedItem['name']."\"");
            $seller = $orderedItem['provider'];
        } else {
            $title = _("New order");
        }

        $this->navbarOrder($title);

        // seller
        $this->content['main'][] = self::divBox(_("Seller"), $seller['@type'], [ self::relationshipOneToOne("order", null, "seller", $seller['@type'], $seller) ]);
        // order
        $this->content['main'][] = self::divBox(_("Order"), "order", [ self::formOrder("new", null, $orderedItem) ]);

        return $this->content;
    }

    private function formOrder($case = "new", $value = null, $orderedItem = null): array
    {
        if ($orderedItem) {
            $orderedItemId = PropertyValue::extractValue($orderedItem['identifier'], "id");
            $providerId = PropertyValue::extractValue($orderedItem['provider']['identifier'], "id");
            $content[] = self::input("orderedItem", "hidden", $orderedItemId);
            $content[] = self::input("orderedItemType", "hidden", lcfirst($orderedItem['@type']));
            $content[] = self::input("seller", "hidden", $providerId);
        }

        // ORDER DATE
        $content[] = self::fieldsetWithInput(_("Order date"), "orderDate", $value['orderDate'] ?? date("Y-m-d"), [], "date");

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

        $content[] = self::fieldsetWithInput(_("Payment due date"), "paymentDueDate", $value['paymentDueDate'], [], "date");

        $content[] = self::submitButtonSend();

        return self::form("/admin/order/$case", $content);
    }
}