<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Invoice;

use Plinct\Tool\ToolBox;

class InvoiceView extends InvoiceAbstract
{
  /**
   * @param array $data
   * @return array
   */
  public function edit(array $data): array
  {
		$typeBuilderOrder = ToolBox::typeBuilder($data);
    $this->idorder = $typeBuilderOrder->getId();
    $lenght = isset($data['partOfInvoice']) ? count($data['partOfInvoice']): 0;
    // NEW
    $content[] = parent::formInvoice("new", null, $lenght+1 );
    // INVOICES
    if ($lenght > 0) {
      foreach ($data['partOfInvoice'] as $key => $value) {
				$paymentDueDate = $value['paymentDueDate'];
				$scheduledPaymentDate = $value['scheduledPaymentDate'];
				$totalPaymentDue = $value['totalPaymentDue'];
        // SET TOTALS AMOUNT
        $this->totalInvoiceAmount += $totalPaymentDue;
        $this->totalPaidAmount += $paymentDueDate !== '0000-00-00' ? $totalPaymentDue : 0;
        $this->totalPayableAmount += $paymentDueDate == '0000-00-00' ? $totalPaymentDue : 0;
        $this->totalPastDueAmount += $paymentDueDate == '0000-00-00' && date("Y-m-d") > $scheduledPaymentDate ? $totalPaymentDue : 0;
        // FORM
        $content[] = parent::formInvoice('edit', $value, $lenght - $key);
      }
    }
    // balance
    $content[] = parent::balance();
		//
    return $content;
  }
}
