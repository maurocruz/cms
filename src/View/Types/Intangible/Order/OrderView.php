<?php
namespace Plinct\Cms\View\Types\Intangible\Order;

use Plinct\Cms\View\Html\Page\ViewInterface;
use Plinct\Cms\View\Types\Intangible\HistoryView;
use Plinct\Cms\View\Types\Intangible\Invoice\InvoiceView;
use Plinct\Cms\View\Types\Intangible\OrderItem\OrderItemView;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Tool\ArrayTool;

class OrderView extends OrderWidget implements ViewInterface {

    public function index(array $data): array {
        $this->navbarOrder();
        // SEARCH
        $this->content['main'][] = self::search("","search",$_GET['search'] ?? null);
        // LIST
            // ordered items
            foreach ($data['itemListElement'] as $key => $value) {
                $orderedItem = $value['item']['orderedItem'];
                $name = $orderedItem[0]['orderedItem']['name'];
                $count = $orderedItem ? count($orderedItem) : '0';
                $text = $count > 1 ? sprintf(_("<b>%s</b> more %s items"), $name, $count) : ($count == 1 ? $name : sprintf(_("%s items"), $count));
                $data['itemListElement'][$key]['item']['orderedItem'] = $text;
            }
        // LIST
        $this->content['main'][] = self::listAll($data, "order", null, [ "customer" => _("Customer"), "seller" => _("Seller"), "orderedItem" => _("Ordered item"), "orderDate" => _("Order date"), "orderStatus" => _("Order status") ]);
        return $this->content;
    }

    public function new($data = null): array {
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

    public function edit(array $data): array {
        $this->content['main'][] = parent::noContent("Method not done!");
        return $this->content;
    }

    public function indexWithPropertyOf($value): array {
        $rowsColumns = [
            "seller" => _("Seller"),
            "customer" => _("Customer"),
            "orderStatus" => [ _("Order status"), [ "style" => "width: 140px;" ] ],
            "dateCreated" => [ _("Date created"), [ "style" => "width: 140px;" ] ]
        ];
        $this->content['main'][] = HtmlPiecesTrait::indexWithSubclass($value['name'], "orders", $rowsColumns, $value['orders']['itemListElement']);
        return $this->content;
    }

    public function newWithPropertyOf($data = null): array {
        $this->content['main'][] = self::divBox2(sprintf(_("Add new %s from %s"), _("order"), $data['seller']['name']), [ self::formOrder("new", $data) ]);
        return $this->content;
    }

    public function editWithPropertyOf(array $data): array {
        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            self::$idOrder = ArrayTool::searchByValue($data['identifier'], "id")['value'];
            // ORDER
            $this->content['main'][] = self::divBox(_("Order"), "order", [ self::formOrder("edit", $data) ]);
            // ORDERED ITEMS
            $this->content['main'][] = self::divBox(_("Ordered items"), "orderItem", [ (new OrderItemView())->edit($data) ]);
            // INVOICES
            $this->content['main'][] = self::divBox(_("Invoices"), "invoice", [ (new InvoiceView())->edit($data) ]);
            // HISTORY
            $this->content['main'][] = self::divBox2(_("Historic"), [ (new HistoryView())->view($data['history']) ]);
        }
        return $this->content;
    }
}
