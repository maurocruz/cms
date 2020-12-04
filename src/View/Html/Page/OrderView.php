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
        return [];
    }

    public function edit(array $data): array
    {
        return [];
    }

    public function new($value = null): array
    {
        if ($value['orderedItem']) {
            $title = sprintf(_("New order for %s"), $value['orderedItem']['@type']." \"".$value['orderedItem']['name']."\"");
        } else {
            $title = _("New order");
        }

        $this->navbarOrder($title);

        $this->content['main'][] = self::divBox($title, "order", [ self::formOrder("new", null, $value['orderedItem']) ]);

        return $this->content;
    }

    private function formOrder($case = "new", $value = null, $orderedItem = null)
    {
        if ($orderedItem) {
            $orderedItemId = PropertyValue::extractValue($orderedItem['identifier'], "id");
            $content[] = self::input("orderedItem", "hidden", $orderedItemId);
        }

        // ORDER DATE
        $content[] = self::fieldsetWithInput(_("Order date"), "orderDate", $value['orderDate'], [], "date");

        // ORDER STATUS
        $content[] = self::fieldsetWithSelect(_("Order status"), "orderStatus", $value['orderStatus'], [
            "OrderCancelled" => _("Order Cancelled"),
            "OrderDelivered" => _("Order delivered"),
            "OrderInTransit" => _("Order in transit"),
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