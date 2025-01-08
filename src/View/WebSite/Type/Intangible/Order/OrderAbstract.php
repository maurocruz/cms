<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Order;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Cms\View\WebSite\Type\Person\Person;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\DateTime;
use Plinct\Tool\StringTool;
use Plinct\Tool\ToolBox;

abstract class OrderAbstract implements TypeViewInterface
{
  /**
   * @var ?string
   */
  protected static ?string $idOrder = null;

	protected int $idthingSeller;
  /**
   * @var int
   */
  protected static int $total;
  /**
   * @var string
   */
  protected string $typeHasPart;
  /**
   * @var string
   */
  protected string $idHasPart;
	/**
	 * @var ?string
	 */
	protected ?string $tags = null;

	/**
	 * @param array $value
	 * @return void
	 */
	public function navbarIndex(array $value)
	{
		if ($value['@type'] == 'Order') {
			$seller = $value['seller'];
			$sellerType = $seller['@type'];
			$sellerName = $seller['name'];
			$sellerTypeBuilder = ToolBox::typeBuilder($seller);
		} else {
			$sellerTypeBuilder = ToolBox::typeBuilder($value);
			$sellerType = $value['@type'];
			$sellerName = $value['name'];
		}
		$this->idthingSeller = $sellerTypeBuilder->getPropertyValue('idthing');
		if ($sellerType == 'Organization') {
			$idorganization = $sellerTypeBuilder->getPropertyValue('idorganization');
			Organization::navbarIndex();
			Organization::navbarEdit($sellerName, $idorganization, $this->idthingSeller);
		}
		if ($sellerType == 'Person') {
			$idperson = $sellerTypeBuilder->getPropertyValue('idperson');
			Person::navbarIndex();
			Person::navbarEdit($sellerName, $idperson);
		}
		$navbar = CmsFactory::view()->fragment()->navbar()
			->type('order')
			->level(4)
			->title(_('Orders'))
			->newTab("/admin/order?seller=$this->idthingSeller", CmsFactory::view()->fragment()->icon()->home(16,16))
			->newTab("/admin/order/new?seller=$this->idthingSeller", CmsFactory::view()->fragment()->icon()->plus(16,16))
			->newTab("/admin/order/invoice?seller=$this->idthingSeller", _('Invoice'))
			->newTab("/admin/order/expired?seller=$this->idthingSeller", ucfirst(_("Due dates")));

		CmsFactory::view()->addHeader($navbar->ready());
	}

	/**
	 * NAVBAR
	 *
	 * @param $value
	 */
  protected function navbarOrder($value)
  {
		self::navbarIndex($value);
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('order')
				->level(5)
				->title(_('Order'))
				->ready()
		);
  }

	protected function navbarInvoice($value)
	{
		self::navbarIndex($value);
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
			->type('invoice')
			->level(5)
			->title(_('Invoice'))
			->newTab("/admin/order/invoice?seller=$this->idthingSeller", CmsFactory::view()->fragment()->icon()->home(16,16))
			->ready()
		);
		/*CmsFactory::view()->fragment()->navbar(_("Payments"),[
			"/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=all" => CmsFactory::view()->fragment()->icon()->home(),
			"/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=past" => _("Until today"),
			"/admin/$this->typeHasPart/order?id=$this->idHasPart&action=payment&period=current_month" => _("Until the end of the current month"),
			"javascript: print();" => _("Print out")
		],5);*/
	}
  /**
   * FORM TO EDIT OR TO ADD A NEW ORDER
   *
   * @param string $case
   * @param null $value
   * @return array
   */
  protected function formOrder(string $case = "new", $value = null): array
  {
		$seller = $case == 'new' ? $value : $value['seller'];
    $form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-order']);
    $form->action("/admin/order/$case")->method('post');
    // hiddens
    if ($case == "edit") $form->input("idorder", self::$idOrder, "hidden");
    // SELLER
    $form->fieldset(CmsFactory::view()->fragment()->form()->chooseType("seller", "organization,person", $seller), _("Seller"));
    // CUSTOMER
    $form->fieldset(CmsFactory::view()->fragment()->form()->chooseType("customer", "localBusiness,organization,person", $value['customer'] ?? null), _("Customer"));
    // ORDER DATE
    $form->fieldsetWithInput("orderDate", isset($value['orderDate']) ? substr($value['orderDate'],0,10) : date("Y-m-d"), _("Order date"), "date");
    // ORDER STATUS
    $form->fieldsetWithSelect("orderStatus", $value['orderStatus'] ?? null, [
      "OrderProcessing" => _("In processing"),
      "OrderInTransit" => _("In transit"),
      "OrderDelivered" => _("Delivered or performed"),
      "OrderPickupAvailable" => _("Pickup available"),
      "OrderSuspended" => _("Suspended"),
      "OrderCancelled" => _("Cancelled"),
      "OrderProblem" => _("With problem"),
      "OrderReturned" => _("Returned")
    ],_("Order status"));
    // PAYMENT DUE DATE
    $form->fieldsetWithInput("paymentDueDate", isset($value['paymentDueDate']) ? substr($value['paymentDueDate'],0,10) : null, _("Payment due date"), "date");
    // DISCOUNT
    $form->fieldsetWithInput("discount", $value['discount'] ?? null, _("Discount"));
    // TAGS
    $form->fieldsetWithInput("tags", $this->tags, _("Tags"));
    // SUBMIT
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/order/erase");
    // READY
    return $form->ready();
  }

  /**
   * @param $numberOfItens
   * @param $section
   * @return array
   */
  protected function selectPeriodo($numberOfItens, $section): array
  {
    $content[] = [ "tag" => "form", "attributes" => [ "class" => "noprint", "action" => "/admin/$this->typeHasPart/order", "method" => "get" ], "content" => [
      [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $this->idHasPart ]],
      [ "tag" => "input", "attributes" => [ "name" => "action", "type" => "hidden", "value" => $section ]],
      [ "tag" => "select", "attributes" => [ "onchange" => "submit();", "name" => "period" ], "content" => [
        [ "tag" => "option", "attributes" => [ "value" => "" ], "content" => _("Select by period") ],
        [ "tag" => "option", "attributes" => [ "value" => "past" ], "content" => _("Until today") ],
        [ "tag" => "option", "attributes" => [ "value" => "current_month" ], "content" => _("Until the end of the current month") ],
        [ "tag" => "option", "attributes" => [ "value" => "all" ], "content" => _("View all") ]
      ] ]
    ] ];

    switch (filter_input(INPUT_GET, 'period')) {
      case "current_month":
        $period = _("Until the end of the current month") . " - <b>".DateTime::translateMonth(date('m'))." ".date('Y')."</b>";
        break;
      case "past":
        $period = _("Until today") . " - <b>".DateTime::formatDate();
        break;
      default :
        $period = null;
        break;
    }

    $content[] = [ "tag" => "p", "content" => sprintf(_("Showing %s items %s"), $numberOfItens, $period) ];

    return [ "tag" => "div", "content" => $content ];
  }

  /**
   * WRITE PARAGRAPH OF THE SELECTED PERIOD
   *
   * @param $period
   * @return string
   */
  protected function periodoParagraph($period): string
  {
    $uri = StringTool::removeDuplicateQueryStrings('period');

    // text
    switch ($period) {
      case '-2 year': $text = 'last 2 years'; break;
      case 'all': $text = 'all'; break;
      default: $text = 'last 5 years'; break;
    }

    $string = "<p class='period-paragraph'>" . sprintf(_('Showing %s'), $text);

    if ($period != 'last2years') {
      $string .= " <a href='$uri&period=last2years'>" . sprintf(_("Show last %s years"),'2') . "</a>";
    }

    if ($period != 'last5years') {
      $string .= " <a href='$uri&period=last5years'>" . sprintf(_("Show last %s years"),'5') . "</a>";
    }

    if($period != 'all') {
      $string .= " <a href='$uri&period=all'>" . _("Show all") . "</a>";
    }

    $string .= "</p>";

    return $string;
  }
}
