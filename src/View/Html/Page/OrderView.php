<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;
use Plinct\Tool\DateTime;

class OrderView implements ViewInterface {
    private array $content = [];
    private static ?string $idOrder;
    private static string $total;

    use FormElementsTrait;

    private function navbarOrder($title = null, $list = null, $level = 2) {
        $title = $title ?? _("Order");
        $list = $list ?? [
            "/admin/order" => _("List all"),
            "/admin/order/new" => _("Add new"),
            "/admin/order/payment" => ucfirst(_("payments")),
            "/admin/order/expired" => ucfirst(_("Due dates"))
        ];
        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level);
    }

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

    private function formOrder($case = "new", $value = null, $orderedItem = null): array {
        $content[] = $case == "edit" ? self::input("id", "hidden", self::$idOrder) : null;
        if ($orderedItem) {
            $orderedItemId = PropertyValue::extractValue($orderedItem['identifier'], "id");
            $providerId = PropertyValue::extractValue($orderedItem['provider']['identifier'], "id");
            $content[] = self::input("orderedItem", "hidden", $orderedItemId);
            $content[] = self::input("orderedItemType", "hidden", lcfirst($orderedItem['@type']));
            $content[] = self::input("seller", "hidden", $providerId);
        }
        // CUSTOMER
        $content[] = self::fieldset(self::chooseType("customer", "localBusiness,organization,person", $value['customer'] ?? null), _("Customer"), [ "style" => "width: 100%;" ]);
        // SELLER
        $content[] = self::fieldset(self::chooseType("seller", "organization,person", $value['seller'] ?? null), _("Seller"), [ "style" => "width: 100%;" ]);
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
        // TIPO (DEPRECATED)
        //$ContractTypes = [ "Não definido", "Hospedagem de Domínio", "Inserção com Vínculo", "Subdomínio", "Banner", "Inserção sem Vínculo" ];
        //$tipo = isset($value['tipo']) ? $ContractTypes[$value['tipo']] : null;
        //$content[] = isset($value['tipo']) ? self::fieldsetWithInput("Tipo ".$value['tipo']." (deprecated)", "tipo", $tipo, null, "text", [ "disabled" ]) : null;
        // TAGS
        $content[] = self::fieldsetWithInput(_("Tags"), "tags", $value['tags'] ?? null, [ "style" => "width: 100%;" ]);
        $submitAttributes = $case == "edit" ? [ "onclick" => "return setHistory(this.parentNode);" ] : null;
        $content[] = self::submitButtonSend($submitAttributes);
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/order/erase") : null;
        return self::form("/admin/order/$case", $content);
    }

    public function payment($data): array {
        $key = 0;
        $this->navbarOrder();
        $content[] = [ "tag" => "h3", "content" => ucfirst(_("payments")) ];
        $content[] = self::selectPeriodo(count($data), "payment");
        $total = 0;
        foreach ($data as $key => $value) {
            $idorder = $value['idorder'];
            $order = "<a href=\"/admin/order/edit/$idorder\">"._("Edit")."</a>";
            $orderStatus = _($value['orderStatus']);
            $paymentDueDate = DateTime::formatDate($value['paymentDueDate']);
            $totalPaymentDue = number_format($value['totalPaymentDue'],2,",",".");
            $customerName = $value['customer']['name']." <a href=\"/admin/order/edit/".$value['idorder']."\">-></a>";
            $invoiceInstallment = $value['numberOfTheInstallments']." / ".$value['totalOfInstallments'];
            $orderedItem = $value['orderedItem'];
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $order],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $idorder],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $paymentDueDate],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $totalPaymentDue ],
                [ "tag" => "td", "content" => $customerName ],
                [ "tag" => "td", "content" => $invoiceInstallment ],
                [ "tag" => "td", "content" => self::getOrderedItems($orderedItem) ],
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
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        return $this->content;
    }

    public function expired($data): array {
        $tbody = null;
        $this->navbarOrder();
        $content[] = [ "tag" => "h3", "content" => _("Expired or due orders") ];
        $content[] = self::selectPeriodo($data['numberOfItems'], "expired");
        foreach ($data['itemListElement'] as $key => $value) {
            $item = $value['item'];
            $id = $item['idorder'];
            //var_dump($item);
            $tbody[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => "<a href='/admin/order/edit/$id'>"._("Edit")."</a>" ],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => $id ],
                [ "tag" => "td", "attributes" => [ "style" => "text-align: right"], "content" => DateTime::formatDate($item['paymentDueDate']) ],
                [ "tag" => "td", "content" => $item['customer']['name'] ],
                [ "tag" => "td", "content" => self::getOrderedItems($item['orderedItem'])],
                [ "tag" => "td", "content" => _($item['orderStatus'])]
            ]];
        }
        $content[] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => [
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "attributes" => [ "style" => "width: 45px;" ], "content" => _("Action") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 50px;" ], "content" => _("ID") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 80px;" ], "content" => _("Due date") ],
                    [ "tag" => "th", "content" => _("Customer") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 240px;" ], "content" => _("Order item") ],
                    [ "tag" => "th", "attributes" => [ "style" => "width: 145px;" ], "content" => _("Status") ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $tbody ]
        ] ];

        $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];
        $this->content['main'][] = [ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ];
        return $this->content;
    }

    private static function getOrderedItems($orderedItem): string {
        if (empty($orderedItem)) {
            return _("Unidentified");
        } else {
            $quantityItems = count($orderedItem);
            $firstItemName = $orderedItem[0]['orderedItem']['name'];
            return $firstItemName . ($quantityItems >1 ? sprintf(_(" more % items."), $quantityItems-1) : null);
        }
    }

    static private function selectPeriodo($numberOfItens, $section): array {
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
        $content[] = [ "tag" => "p", "content" => sprintf(_("Showing %s items %s"), $numberOfItens, $period) ];

        return [ "tag" => "div", "content" => $content ];
    }
}
