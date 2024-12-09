<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Order;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\OrderItem\OrderItemView;
use Plinct\Tool\DateTime;
use Plinct\Tool\ToolBox;

use Plinct\Cms\View\WebSite\Type\Intangible\HistoryView;

class OrderView extends OrderAbstract
{

	public function index(?array $value)
	{
		$typeBuilder = ToolBox::typeBuilder($value);
		$idthing = $typeBuilder->getPropertyValue('idthing');
		parent::navbarIndex($value);
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('order')->setHasPart($idthing)->ready()
		);
	}

	/**
	 * CREATE NEW ORDER
	 *
	 * @param null $value
	 */
	public function new($value = null)
	{
		// NAVBAR
		parent::navbarIndex($value);
		// FORM NEW
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formOrder("new", $value), sprintf(_("Add new %s from %s"), _("order"), $value['name']))
		);
	}

  /**
   * EDIT A ORDER
   *
   * @param ?array $data
   */
  public function edit(?array $data)
  {
		if (empty($data)) {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->noContent());
		} else {
			$value = $data[0];
			$typeBuilder = ToolBox::typeBuilder($value);
			self::$idOrder = $typeBuilder->getId();
			$this->tags = $typeBuilder->getPropertyValue('tags');
      // NAVBAR
      parent::navbarOrder($value);
      // ORDER
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formOrder("edit", $value), _("Order")));
      // ORDERED ITEMS
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox((new OrderItemView())->edit($value), _("Ordered items")));
      // INVOICES
      //CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox((new InvoiceView())->edit($value), _("Invoices")));
      // HISTORY
      //CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox((new HistoryView())->view($value['history']), _("Historic")));
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

    CmsFactory::view()->fragment()->navbar(_("Payments"),[
      "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=all" => CmsFactory::view()->fragment()->icon()->home(),
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

    CmsFactory::view()->addMain([ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ]);
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

    CmsFactory::view()->fragment()->navbar(_("Expired orders"),[
      "/admin/$this->typeHasPart/order?id=$this->idHasPart&action=expired&period=all" => CmsFactory::view()->fragment()->icon()->home(),
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
    $table = CmsFactory::view()->fragment()->listTable();
    $table->caption(sprintf(_("List of %s"), _("orders")));
    $table->labels('ID', _("Due date"), _("Customer"), _("Ordered item"), _("Order status"));
    $table->rows($orders['itemListElement'],['idorder', 'paymentDueDate', 'customer', 'orderedItem:0:orderedItem', 'orderStatus'])
    ->setEditButton("/admin/organization/order?id=$idHasPart&item=");
    $content[] = $table->ready();

    // PRINT
    $content[] = [ "tag" => "p", "content" => "Imprimir", "href" => "javascript: void(0);", "hrefAttributes" => [ "onclick" => "print();" ] ];

    // VIEW
    CmsFactory::view()->addMain([ "tag" => "div", "attributes" => [ "class" => "box" ], "content" => $content ]);
  }
}
