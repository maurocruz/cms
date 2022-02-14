<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Order;

use App\Tools\ToolBox;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\Intangible\HistoryView;
use Plinct\Cms\WebSite\Type\Intangible\Invoice\InvoiceView;
use Plinct\Cms\WebSite\Type\Intangible\OrderItem\OrderItemView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;

class OrderView extends OrderAbstract
{
    /**
     * LIST ORDERS
     *
     * @param $value
     */
    public function indexWithPartOf($value)
    {
        if (isset($value['orders']['error']) || (isset($value['orders']['status']) && $value['orders']['status'] == 'error')) {
            $message = $value['orders']['error']['message'] ?? $value['orders']['message'] ?? "error";
            View::main(Fragment::error()->installSqlTable('order', $message));

        } else {
            $orders = $value['orders'];
            $idSeller = ToolBox::searchByValue($value['identifier'],'id','value');
            // NAVBAR
            parent::navbarOrder($value);
            // SEARCH
            View::main(Fragment::form()->search("", "customerName", filter_input(INPUT_GET, 'customerName')));
            // PERIOD
            View::main(parent::periodoParagraph($orders['itemListOrder']));
            // LIST TABLE
            $table = Fragment::listTable(['class'=>'table-list']);
            $table->caption(sprintf(_("List of %s"), _("orders")))
                ->labels("ID", _("Customer"), _("Seller"), _("Ordered items"), _("Order status"), _("Order date"))
                ->setProperties(['idorder','customer','seller','orderedItem','orderStatus','orderDate']);

            foreach ($orders['itemListElement'] as $orderItem) {
                $item = $orderItem['item'];
                $idIsPartOf = $item['idorder'];
                $tableIsPartOf = "order";
                $idHasPart = ToolBox::searchByValue($item['seller']['identifier'],'id','value');
                $tableHasPart = lcfirst($item['seller']['@type']);
                $orderedItems = [];
                if (isset($item['orderedItem'])) {
                    foreach ($item['orderedItem'] as $orderedItem) {
                        $orderedItems[] = $orderedItem['orderedItem']['name'];
                    }
                }
                $table->addRow($item['idorder'], $item['customer']['name'], $item['seller']['name'], implode("; ",$orderedItems), $item['orderStatus'], $item['orderDate'])
                    ->buttonEdit("/admin/organization/order?id=$idSeller&item={$item['idorder']}")
                    ->buttonDelete($idIsPartOf, $tableIsPartOf, $idHasPart, $tableHasPart);
                unset($orderedItems);
            }

            // ready
            View::main($table->ready());
        }
    }

    /**
     * CREATE NEW ORDER
     *
     * @param null $value
     */
    public function newWithPartOf($value = null)
    {
        // NAVBAR
        parent::navbarOrder($value);

        // FORM NEW
        View::main(Fragment::box()->simpleBox(self::formOrder("new", [ 'seller' => $value ]), sprintf(_("Add new %s from %s"), _("order"), $value['name'])));
    }

    /**
     * EDIT A ORDER
     *
     * @param array $data
     */
    public function editWithPartOf(array $data)
    {
        if (empty($data)) {
            View::main(Fragment::noContent());

        } else {
            self::$idOrder = (int) ArrayTool::searchByValue($data['identifier'],'id','value');
            // NAVBAR
            parent::navbarOrder($data['seller'],$data['customer']['name']);
            // ORDER
            View::main(Fragment::box()->simpleBox(self::formOrder("edit", $data), _("Order")));
            // ORDERED ITEMS
            View::main(Fragment::box()->simpleBox((new OrderItemView())->edit($data), _("Ordered items")));
            // INVOICES
            View::main(Fragment::box()->simpleBox((new InvoiceView())->edit($data), _("Invoices")));
            // HISTORY
            View::main(Fragment::box()->simpleBox((new HistoryView())->view($data['history']), _("Historic")));
        }
    }

    /**
     * SHOW PAYMENT INVOICES WHICH DUE DATE EXPIRED OR NEXT TO EXPIRY
     *
     * @param $value
     */
    public function payment($value)
    {
        // NAVBAR
        parent::navbarOrder($value);

        View::navbar(_("Payments"),[
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=all" => Fragment::icon()->home(),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=past" => _("Until today"),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=current_month" => _("Until the end of the current month"),
            "javascript: print();" => _("Print out")
        ],5);

        // VARS
        $key = 0;

        // TITLE
        $content[] = [ "tag" => "h3", "content" => ucfirst(_("payments")) ];

        // SELECT PERIOD
        $content[] = parent::selectPeriodo(count($value['orders']), "payment");

        $total = 0;
        foreach ($value['orders'] as $key => $value) {
            //var_dump($value);
            $idorder = $value['idorder'];
            $href = "/admin/$this->typeHasPart/order?id=$this->idHasPart&item=$idorder";
            $orderStatus = _($value['orderStatus']);
            $paymentDueDate = DateTime::formatDate($value['paymentDueDate']);
            $totalPaymentDue = number_format((float)$value['totalPaymentDue'],2,",",".");
            $customerName = $value['customerName'];
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => sprintf('<a href="%s">%s</a>', $href, _("Edit")) ],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $idorder],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $paymentDueDate],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $totalPaymentDue ],
                [ "tag" => "td", "content" => $customerName ],
                [ "tag" => "td", "content" => $value['installments'] ],
                [ "tag" => "td", "content" => $value['orderedItems'] ],
                [ "tag" => "td", "content" => $orderStatus ]
            ]];
            $total += $value['totalPaymentDue'];
        }

        // total
        $tbody[] = [ "tag" => "tr", "attributes" => [ "style" => "background-color: rgba(0,0,0,0.65);" ], "content" => [
            [ "tag" => "td", "attributes" => [ "colspan" => "2"], "content" => "" ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: center"], "content" => "TOTAL" ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => number_format($total,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: center"], "content" => ($key+1). " itens" ],
            [ "tag" => "td", "content" => "" ],
            [ "tag" => "td", "content" => "" ],
            [ "tag" => "td", "content" => "" ]
        ]];

        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 30px;" ], "content" => _("Action") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 30px;" ], "content" => _("ID") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => _("Due date") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 90px;" ], "content" => _("Values") ],
                    [ "tag" => "th", "attributes" => [ "style" => "min-width: 240px;" ], "content" => _("Customer") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => ("Installments") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 240px;" ], "content" => _("Item") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 140px;" ], "content" => _("Status") ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $tbody ]
        ] ];

        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];

        View::main([ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ]);
    }

    /**
     * SHOW ORDERS WHOSE DUE DATE HAS EXPIRED
     *
     * @param $value
     */
    public function expired($value)
    {
        // NAVBAR
        parent::navbarOrder($value);

        View::navbar(_("Expired orders"),[
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=all" => Fragment::icon()->home(),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=past" => _("Until today"),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=current_month" => _("Until the end of the current month"),
            "javascript: print();" => _("Print out")
        ],5);

        // VARS
        $idHasPart = ToolBox::searchByValue($value['identifier'],'id','value');
        $orders = $value['orders'];

        // TITLE
        $content[] = [ "tag" => "h3", "content" => _("Expired or due orders") ];

        // SELECT BY PERIOD
        $content[] = self::selectPeriodo($orders['numberOfItems'], "expired");

        // TABLE
        $table = Fragment::listTable();
        $table->caption(sprintf(_("List of %s"), _("orders")));
        $table->labels('ID', _("Due date"), _("Customer"), _("Ordered item"), _("Order status"));
        $table->rows($orders['itemListElement'],['idorder', 'paymentDueDate', 'customer', 'orderedItem:0:orderedItem', 'orderStatus'])
        ->setEditButton("/admin/organization/order?id=$idHasPart&item=");
        $content[] = $table->ready();

        // PRINT
        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];

        // VIEW
        View::main([ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ]);
    }
}
