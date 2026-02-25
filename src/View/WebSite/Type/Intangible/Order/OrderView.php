<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Order;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\OrganizationView;
use Plinct\Tool\ToolBox;

class OrderView extends OrganizationView
{
	private ?string $idorder = null;
	private ?string $tags;


	public function __construct(string $type = 'organization', string $sitemapFilename = 'sitemap-organization.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct()
	{
		parent::__destruct();

		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('order')
				->level(4)
				->title(_('Orders'))
				->newTab("/admin/order?seller=$this->organizationThing", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/order/new?seller=$this->organizationThing", CmsFactory::view()->fragment()->icon()->plus())
				->newTab("/admin/invoice?provider=$this->organizationThing", _('Invoices'))
				->newTab("/admin/order?seller=$this->organizationThing&paymentDueDate=<curdate&orderStatus=OrderProcessing|OrderSuspended", ucfirst(_("Expired orders")))
				->newTab("/admin/order?seller=$this->organizationThing&paymentDueDate=>curdate&orderStatus=OrderProcessing|OrderSuspended", ucfirst(_("Orders due")))
			->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		$typeBuilder = ToolBox::typeBuilder($data);
		$this->idthing = $typeBuilder->getIdthing();
		$this->organizationThing = $this->idthing;
		$this->name = $typeBuilder->getValue('name');
		if ($typeBuilder->getType() == 'Organization') {
			$this->idorganization = $typeBuilder->getId();
			$this->organizationThing = $this->idthing;
		}
		$reactShell = CmsFactory::view()->fragment()->reactShell('order')->setDataset("seller",$this->organizationThing);
		if (isset($queryParams['paymentDueDate'])) {
			$reactShell->setDataset('paymentDueDate',$queryParams['paymentDueDate']);
		}
		if (isset($queryParams['orderStatus'])) {
			$reactShell->setDataset('orderStatus',$queryParams['orderStatus']);
		}
		CmsFactory::view()->addMain(
			$reactShell->ready()
		);
	}

	/**
	 * CREATE NEW ORDER
	 *
	 * @param null $data
	 * @param array|null $queryParams
	 */
	public function new($data = null, array $queryParams = null): void
	{
		// FORM NEW
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->box()->simpleBox(self::formOrder("new", $data), sprintf(_("Add new %s from %s"), _("order"), $data['name']))
		);
	}

  /**
   * EDIT An ORDER
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function edit(?array $data, array $queryParams = null): void
  {
		if (empty($data)) {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->noContent());
		} else {
			$value = $data[0];
			$typeBuilder = ToolBox::typeBuilder($value);
			$this->idorder = $typeBuilder->getId();
			$this->tags = $typeBuilder->getPropertyValue('tags');
			$seller = $value['seller'];
			$tbSeller = ToolBox::typeBuilder($seller);
			$this->organizationThing = $tbSeller->getIdthing();
			$this->name = $seller['name'];
			$this->idorganization = $tbSeller->getId();
			$this->idthing = $tbSeller->getIdthing();
      // ORDER FORM
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formOrder("edit", $value), _("Order"), $this->idorder));
      // ORDER DATAILS
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('order')->setDataset("idorder",$this->idorder)->ready());
    }
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
		$form = CmsFactory::view()->fragment()->form("form-order", ['class'=>'form-basic form-order']);
		$form->action("/admin/order/$case")->method('post');
		$form->setIdform("form-order-".($this->idorder ?? "new"));
		$form->addMandatories(['seller','customer','orderDate','orderStatus','paymentDueDate']);
		// hiddens
		if ($case == "edit") $form->input("idorder", $this->idorder, "hidden");
		// SELLER
		if ($case == 'new') {
			$form->chooseType(_("Seller"), "seller", "organization,person", $seller);
		} else {
			$form->fieldsetWithInput('seller',$seller['name'],_('Seller'),'text',null,['disabled'=>'disabled']);
		}
		// CUSTOMER
		$form->chooseType(_("Customer"), "customer", "localBusiness,organization,person", $value['customer'] ?? null);
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
}
