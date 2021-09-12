<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Order;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\Intangible\HistoryView;
use Plinct\Cms\View\Types\Intangible\Invoice\InvoiceView;
use Plinct\Cms\View\Types\Intangible\OrderItem\OrderItemView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
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
        $orders = $value['orders'];

        // NAVBAR
        parent::navbarOrder($value);

        // SEARCH
        View::main( Fragment::form()->search("","customerName", filter_input(INPUT_GET,'customerName')));

        // PERIOD
        View::main(parent::periodoParagraph($orders['itemListOrder']));

        // TABLE
        $rowsColumns = [
            "idorder" => [ "ID", [ "style" => "width: 50px;" ] ],
            "customer" => _("Customer"),
            "seller" => _("Seller"),
            "orderedItem" => _("Item ordered"),
            "orderStatus" => [ _("Order status"), [ "style" => "width: 140px;" ] ],
            "orderDate" => [ _("Order date"), [ "style" => "width: 100px;" ] ]
        ];

        View::main( HtmlPiecesTrait::indexWithSubclass($value, "order", $rowsColumns, $orders['itemListElement']) );
    }

    /**
     * CREATE NEW ORDER
     *
     * @param null $data
     */
    public function newWithPartOf($data = null)
    {
        $value = $data['seller'];

        // NAVBAR
        parent::navbarOrder($value);

        // FORM NEW
        View::main(self::divBox2(sprintf(_("Add new %s from %s"), _("order"), $value['name']), [ self::formOrder("new", $data) ]));
    }

    /**
     * EDIT A ORDER
     *
     * @param array $data
     */
    public function editWithPartOf(array $data)
    {
        if (empty($data)) {
            View::main(self::noContent());

        } else {
            self::$idOrder = (int) ArrayTool::searchByValue($data['identifier'],'id','value');

            // NAVBAR
            parent::navbarOrder($data['seller'],$data['customer']['name']);

            // ORDER
            View::main(self::divBox(_("Order"), "order", [ self::formOrder("edit", $data) ]));

            // ORDERED ITEMS
            View::main(self::divBox(_("Ordered items"), "orderItem", [ (new OrderItemView())->edit($data) ]));

            // INVOICES
            View::main(self::divBox(_("Invoices"), "invoice", [ (new InvoiceView())->edit($data) ]));

            // HISTORY
            View::main(self::divBox2(_("Historic"), [ (new HistoryView())->view($data['history']) ]));
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

        View::navbar(_("Expired"),[
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=all" => Fragment::icon()->home(),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=past" => _("Until today"),
            "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=current_month" => _("Until the end of the current month"),
            "javascript: print();" => _("Print out")
        ],5);

        // VARS
        $orders = $value['orders'];

        $rowColunms = [
            "idorder" => [ "ID", [ "style" => "width: 50px;" ] ],
            "paymentDueDate" => [ _("Due date"), [ "style" => "width: 82px;" ] ],
            "customer" => _("Customer"),
            "orderedItem" => _("Ordered item"),
            "orderStatus" => _("Order status")
        ];

        // TITLE
        $content[] = [ "tag" => "h3", "content" => _("Expired or due orders") ];

        // SELECT BY PERIOD
        $content[] = self::selectPeriodo($orders['numberOfItems'], "expired");

        // TABLE
        $content[] = HtmlPiecesTrait::indexWithSubclass($value, "order", $rowColunms, $orders['itemListElement']);

        // PRINT
        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];

        // VIEW
        View::main([ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ]);
    }
}
