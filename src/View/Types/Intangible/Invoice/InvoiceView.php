<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Invoice;

class InvoiceView extends InvoiceAbstract
{
    /**
     * @param array $data
     * @return array
     */
    public function edit(array $data): array
    {
        $this->idorder = (int) $data['idorder'];
        $lenght = $data['partOfInvoice'] ? count($data['partOfInvoice']): 0;

        // NEW
        $content[] = parent::formInvoice("new", null, $lenght+1 );

        // INVOICES
        if ($lenght > 0) {
            foreach ($data['partOfInvoice'] as $key => $value) {
                // SET TOTALS AMOUNT
                $this->totalInvoiceAmount += $value['totalPaymentDue'];
                $this->totalPaidAmount += $value['paymentDate'] !== '0000-00-00' ? $value['totalPaymentDue'] : 0;
                $this->totalPayableAmount += $value['paymentDate'] == '0000-00-00' ? $value['totalPaymentDue'] : 0;
                $this->totalPastDueAmount += $value['paymentDate'] == '0000-00-00' && date("Y-m-d") > $value['paymentDueDate'] ? $value['totalPaymentDue'] : 0;

                // FORM
                $content[] = parent::formInvoice('edit', $value, $lenght - $key);
            }
        }

        // balance
        $content[] = parent::balance();

        return $content;
    }
}
