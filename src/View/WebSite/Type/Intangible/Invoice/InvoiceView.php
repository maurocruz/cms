<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Invoice;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\Order\OrderView;
use Plinct\Tool\ToolBox;

class InvoiceView extends OrderView
{
	public function __construct(string $type = 'invoice', string $sitemapFilename = 'sitemap-invoice.xml')
	{
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct()
	{
		parent::__destruct();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('invoice')
				->title(_("Invoice"))
				->newTab("/admin/invoice", _("All invoices"))
				->newTab("/admin/invoice?provider=$this->organizationThing&paymentStatus=paymentDue", _("Invoices due"))
				->newTab("/admin/invoice?provider=$this->organizationThing&paymentStatus=paymentDue&scheduledPaymentDate=<curdate", _("Invoices overdue"))
				->level(5)
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
		$tbProvider = ToolBox::typeBuilder($data);
		$this->idthing = $tbProvider->getIdthing();
		$this->name = $data['name'];
		$this->idorganization = $tbProvider->getId();
		$this->organizationThing = $this->idthing;
		$reactShell = CmsFactory::view()->fragment()->reactShell('invoice')->setDataset('provider',$this->idthing);
		if (isset($queryParams['paymentStatus'])) {
			$reactShell->setDataset('paymentStatus',$queryParams['paymentStatus']);
		}
		if (isset($queryParams['scheduledPaymentDate'])) {
			$reactShell->setDataset('scheduledPaymentDate',$queryParams['scheduledPaymentDate']);
		}
		CmsFactory::view()->addMain($reactShell->ready());
	}
}
