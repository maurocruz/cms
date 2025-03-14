<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Invoice;

use NumberFormatter;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class InvoiceView extends InvoiceAbstract implements TypeViewInterface
{
	/**
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value): void
	{
		$provider = $value['provider'] ?? null;
		if ($provider) {
			parent::navbarIndex($provider);
		}
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('invoice')->setIdHasPart($this->providerIdthing)->setOrderBy('schedulePaymentDate')->ready()
		);
	}

	public function edit(?array $data): void
	{
		if (!empty($data)) {
			$provider = $data['provider'] ?? null;
			$customer = $data['customer'] ?? null;
			$order = $data['referencesOrder'] ?? null;
			$this->setCustomer($customer);
			$this->setOrder($order);
			$this->navbarIndex($provider);

			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox(
					[
						"<p>"
							._('Provider').": <a href='/admin/".lcfirst($this->providerType)."/edit/".$this->providerId."'>".$this->providerName."</a>; "
							._('Customer').": <a href='/admin/".lcfirst($this->customerType)."/edit/".$this->customerId."'>".$this->customerName."</a>; "
							._('Order').": <a href='/admin/order/edit/".$this->idorder."'>"._('Order')."</a></p>",
						parent::formInvoice('edit', $data)
					],
					_('Invoice')
				)
			);
		} else {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->message()->noContent()
			);
		}
	}

	/**
	 * @param ?array $data
	 * @return array
	 */
	public function editWithPart(?array $data): array
	{
		$value = $data[0] ?? $data;
		$provider =  $value['@type'] == 'Order' ? $value['seller'] : $value['provider'];
		$this->setCustomer($value['customer']);
		$this->setProvider($provider);

		$typeBuilderOrder = ToolBox::typeBuilder($data);
		$this->idorder = $typeBuilderOrder->getId();
		$lenght = isset($value['partOfInvoice']) ? count($value['partOfInvoice']) : 0;
		// NEW
		$content[] = parent::formInvoice("new", null, $lenght + 1);
		// INVOICES
		if ($lenght > 0) {
			foreach ($value['partOfInvoice'] as $key => $item) {
				$paymentDueDate = $item['paymentDueDate'];
				$scheduledPaymentDate = $item['scheduledPaymentDate'];
				$totalPaymentDue = $item['totalPaymentDue'];
				// SET TOTALS AMOUNT
				$this->totalInvoiceAmount += $totalPaymentDue;
				$this->totalPaidAmount += $paymentDueDate !== '0000-00-00' ? $totalPaymentDue : 0;
				$this->totalPayableAmount += $paymentDueDate == '0000-00-00' ? $totalPaymentDue : 0;
				$this->totalPastDueAmount += $paymentDueDate == '0000-00-00' && date("Y-m-d") > $scheduledPaymentDate ? $totalPaymentDue : 0;
				// FORM
				$content[] = parent::formInvoice('edit', $item, $lenght - $key);
			}
		}
		// balance
		$content[] = parent::balance();
		//
		return $content;
	}

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function paymentDue(?array $value): void
	{
		$provider = $value['provider'];
		$invoices = $value['invoices'];
		parent::navbarIndex($provider);

		CmsFactory::view()->addMain("<h3>Faturas abertas</h3>");
		CmsFactory::view()->addMain("<table class='table'>");
		CmsFactory::view()->addMain("<thead><tr><th>#</th><th>"._('Invoice')."</th><th>"._('Order')."</th><th>"._('Data do vencimento')."</th><th>"._('Valor')."</th><th>"._('Customer')."</th><th>"._('Order status')."</th></tr></thead>");
		CmsFactory::view()->addMain("<tbody>");
		$numberFormatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
		$total = 0;
		foreach ($invoices as $key => $invoice) {
			$tbInvoice = ToolBox::typeBuilder($invoice);
			$idinvoice = $tbInvoice->getId();
			$tbOrder = ToolBox::typeBuilder($invoice['referencesOrder']);
			$idorder = $tbOrder->getPropertyValue('idorder');
			$scheduledPaymentDate = $invoice['scheduledPaymentDate'];
			$total += $invoice['totalPaymentDue'];
			$totalPaymentDue = $numberFormatter->format($invoice['totalPaymentDue']);
			$customerName = $invoice['customer']['name'];
			$orderStatus = $invoice['referencesOrder']['orderStatus'];
			CmsFactory::view()->addMain("<tr>");
			CmsFactory::view()->addMain("<td>".($key+1)."</td>");
			CmsFactory::view()->addMain("<td><a href='/admin/invoice/edit/$idinvoice'>"._('Edit invoice')."</a></td>");
			CmsFactory::view()->addMain("<td><a href='/admin/order/edit/$idorder'>"._('Edit order')."</a></td>");
			CmsFactory::view()->addMain("<td>$scheduledPaymentDate</td>");
			CmsFactory::view()->addMain("<td>$totalPaymentDue</td>");
			CmsFactory::view()->addMain("<td>$customerName</td>");
			CmsFactory::view()->addMain("<td>$orderStatus</td>");
			CmsFactory::view()->addMain("</tr>");
		}
		CmsFactory::view()->addMain("<tr style='background-color: rgba(0,0,0,0.65);'><td colspan='4'>TOTAL</td><td colspan='4'>".$numberFormatter->format($total)."</td></tr>");
		CmsFactory::view()->addMain("</tbody>");
		CmsFactory::view()->addMain("</table>");
	}


	public function new(?array $value)
	{
		// TODO: Implement new() method.
	}
}
