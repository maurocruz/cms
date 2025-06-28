<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Order;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\Invoice\InvoiceView;
use Plinct\Cms\View\WebSite\Type\Intangible\OrderItem\OrderItemView;
use Plinct\Cms\View\WebSite\Type\Action\ActionView;
use Plinct\Tool\ToolBox;

class OrderView extends OrderAbstract
{
	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		$typeBuilder = ToolBox::typeBuilder($data);
		$idthing = $typeBuilder->getPropertyValue('idthing');
		parent::navbarIndex($data);
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('order')->setIdHasPart($idthing)->ready()
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
		// NAVBAR
		parent::navbarIndex((array)$data);
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
			self::$idOrder = $typeBuilder->getId();
			$this->tags = $typeBuilder->getPropertyValue('tags');
      // NAVBAR
      parent::navbarOrder($value);
      // ORDER
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formOrder("edit", $value), _("Order")));
      // ORDERED ITEMS
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox((new OrderItemView())->edit($value), _("Ordered items")));
      // INVOICES
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox((new InvoiceView())->editWithPart($value), _("Invoices")));
      // HISTORY
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox((new ActionView())->view($value['potentialAction'] ?? []), _("Historic")));
    }
  }

  /**
   * SHOW ORDERS WHOSE DUE DATE HAS EXPIRED
   *
   * @param $value
   */
  public function expired($value): void
  {
		$seller = $value['seller'];
		$orderData = $value['orders'];
    // NAVBAR
    parent::navbarExpired($seller);
		// TABLE
		$table = CmsFactory::view()->fragment()->table(['class'=>'table-order-expired']);
		$table->setCaption(_("Expired or due orders"));
		$table->labels(
			'#',
			'idorder',
			_("Payment due date"),
			_('Customer'),
			_('Ordered items')
		);
		foreach ($orderData as $key => $order) {
			$tbOrder = ToolBox::typeBuilder($order);
			$idorder = $tbOrder->getId();
			$paymentDueDate = $order['paymentDueDate'];
			$tbCustomer = ToolBox::typeBuilder($order['customer']);
			$customerId = $tbCustomer->getId();
			$customerName = $tbCustomer->getValue('name');
			$customerType = $tbCustomer->getType();
			$orderedItem = $order['orderedItem'] ?? [];
			$itemOrderedArray = [];
			foreach ($orderedItem as $item) {
				$itemOrderedArray[] = $item['orderedItem']['name'];
			}
			$itemOrderedString = implode(', ', $itemOrderedArray);
			// row
			$table->addRow(
				$key+1,
				"<a href='/admin/order/edit/$idorder'>$idorder</a>",
				ToolBox::dateTime($paymentDueDate)->format('d/m/Y'),
				"<a href='/admin/$customerType/edit/$customerId'>$customerName</a>",
				$itemOrderedString
			);
		}
	  // VIEW
	  CmsFactory::view()->addMain($table->ready());
	}
}
