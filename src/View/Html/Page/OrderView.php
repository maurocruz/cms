<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;
use Plinct\Tool\DateTime;

class OrderView implements ViewInterface
{
    private $content = [];

    private static $idOrder;

    private static $total;

    use FormElementsTrait;

    private function navbarOrder($title = null, $list = null, $level = 2)
    {
        $title = $title ?? _("Order");
        $list = $list ?? [
            "/admin/order" => _("List all"),
            "/admin/order/new" => _("Add new"),
            "/admin/order/payment" => ucfirst(_("payments")),
            "/admin/order/expired" => ucfirst(_("expired contracts"))
        ];
        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level);
    }

    public function index(array $data): array
    {
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
            // banner
            $banner = $data['banner'] ?? null;
            $this->content['main'][] = $banner ? (new BannerView())->getBannerByIdcontrato($banner) : null;
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
        // TIPO (DEPRECATED)
        $ContractTypes = [ "Não definido", "Hospedagem de Domínio", "Inserção com Vínculo", "Subdomínio", "Banner", "Inserção sem Vínculo" ];
        $tipo = $value['tipo'] ? $ContractTypes[$value['tipo']] : null;
        $content[] = self::fieldsetWithInput("Tipo ".$value['tipo']." (deprecated)", "tipo", $tipo, null, "text", [ "disabled" ]);
        // TAGS
        $content[] = self::fieldsetWithInput(_("Tags"), "tags", $value['tags'], [ "style" => "width: 100%;" ]);

        $submitAttributes = $case == "edit" ? [ "onclick" => "return setHistory(this.parentNode);" ] : null;
        $content[] = self::submitButtonSend($submitAttributes);

        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/order/erase") : null;

        return self::form("/admin/order/$case", $content);
    }



    public function payment($data): array
    {
        $key = 0;
        $this->navbarOrder();

        $content[] = [ "tag" => "h3", "content" => "Overdue payments" ];
        $content[] = self::selectPeriodo(count($data), "payment");

        $total = 0;
        foreach ($data as $key => $value) {
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => DateTime::formatDate($value['paymentDueDate']) ],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => number_format($value['totalPaymentDue'],2,",",".") ],
                [ "tag" => "td", "content" => $value['name']." <a href=\"/admin/order/edit/".$value['idorder']."\">-></a>" ],
                [ "tag" => "td", "content" => ($key+1)." / ".$value['number_parc'] ],
                [ "tag" => "td", "content" => "<a href=\"/admin/order/edit/".$value['idorder']."\">".$value['contrato_name']."</a>" ],
                [ "tag" => "td", "content" => $value['orderStatus'] == 'orderSuspended' ? 'Suspenso' : ($value['orderStatus'] == 'orderProcessing' ? 'Ativo' : 'Inativo') ]
            ]];
            $total += $value['totalPaymentDue'];
        }
        // total
        $tbody[] = [ "tag" => "tr", "attributes" => [ "style" => "background-color: rgba(0,0,0,0.65);" ], "content" => [
            [ "tag" => "td", "attributes" => [ "style" => "text-align: center"], "content" => "TOTAL" ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => "R$ ".number_format($total,2,",",".") ],
            [ "tag" => "td", "attributes" => [ "style" => "text-align: center"], "content" => ($key+1). " itens" ],
            [ "tag" => "td", "content" => "" ],
            [ "tag" => "td", "content" => "" ],
            [ "tag" => "td", "content" => "" ]
        ]];

        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => "Vencimento" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => "Valor (R$)" ],
                    [ "tag" => "th", "content" => "Local Business" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => "Parcela" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 180px;" ], "content" => _("Contract type") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 180px;" ], "content" => _("Status") ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $tbody ]
        ] ];

        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];

        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];

        return $this->content;
    }

    public function expired($data): array
    {
        $tbody = null;

        $this->navbarOrder();

        $content[] = [ "tag" => "h3", "content" => "Expired contracts" ];
        $content[] = self::selectPeriodo(count($data), "expired");

        foreach ($data as $key => $value) {
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => DateTime::formatDate($value['paymentDueDate']) ],
                [ "tag" => "td", "content" => $value['name'] ],
                [ "tag" => "td", "content" => "<a href=\"/admin/order/edit/".$value['idorder']."\">".$value['contrato_name']."</a>" ]
            ]];
        }

        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 100px;" ], "content" => "Vencimento" ],
                    [ "tag" => "th", "content" => "Local Business" ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 180px;" ], "content" => "Contract type" ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $tbody ]
        ] ];

        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];

        return $this->content;
    }

    static private function selectPeriodo($numberOfItens, $section): array
    {
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
        $content[] = [ "tag" => "p", "content" => "Showing ".$numberOfItens." itens $period" ];

        return [ "tag" => "div", "content" => $content ];
    }
}