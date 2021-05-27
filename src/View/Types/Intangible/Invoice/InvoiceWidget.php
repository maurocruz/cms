<?php
namespace Plinct\Cms\View\Types\Intangible\Invoice;

use DateTime;
use Exception;
use Plinct\Cms\View\Types\Intangible\OrderItem\OrderItemView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

abstract class InvoiceWidget {
    protected $idorder;
    protected static $tableHasPart = null;
    protected static $idHasPart = null;
    protected static $customer;
    protected static $customerType;
    protected static $provider;
    protected static $providerType;
    protected $content = [];
    protected $totalInvoiceAmount = 0;
    protected $totalPaidAmount = 0;
    protected $totalPayableAmount = 0;
    protected $totalPastDueAmount = 0;

    use navbarTrait;
    private static $totalPaymentAmount;

    use FormElementsTrait;

    protected function formInvoice($case = 'new', $value = null, $n = null): array {
        // VARS
        $idinvoice = $value ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        // HIDDEN
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "referencesOrder", "value" => $this->idorder, "type" => "hidden"] ];
        $content[] = self::input("tableHasPart", "hidden", $this->idorder);
        if ($case == "edit") {
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "value" => $idinvoice, "type" => "hidden"] ];
        }
        // #
        $p = $case == "new" ? "+" : $n;
        $content[] = "<span style=\"display: inline-block; width: 30px;\">".$p.".</span>";
        // TOTAL PAYMENT DUE
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 135px; display: inline-block;"], "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Invoice amount") ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "totalPaymentDue", "value" => $value['totalPaymentDue'] ?? null, "type" => "number", "step" => "0.01", "min" => "0.01", "style" => "text-align: right; width: inherit;" ]]
        ]];
        // Payment due date
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 145px; display: inline-block;"], "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Payment due date") ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "paymentDueDate", "value" => $value['paymentDueDate'] ?? null, "type" => "date", "style" => "width: inherit;" ]]
        ]];
        // PAYMENT DATE
        $content[] = [ "tag" => "fieldset", "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Payment date") ] : null,
            self::input('paymentDate','date',$value['paymentDate'] ?? null, [ "style" => "width: 145px;"])
        ]];
        // PAYMENT STATUS
        $content[] = [ "tag" => "fieldset", "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Payment status") ] : null,
            self::select("paymentStatus", $value['paymentStatus'] ?? null, [
                "PaymentDue" => _("Payment due"),
                "PaymentComplete" => _("Payment complete"),
                "PaymentPastDue" => _("Payment past due"),
                "PaymentDeclined" => _("Payment declined"),
                "PaymentAutomaticallyApplied" => _("Payment automatically applied")
            ])
        ]];
        // submit
        $content[] = self::submitButtonSend([ "style" => "height: 30px; background-color: transparent !important; padding: 0 5px; border: 0;"]);
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/invoice/erase", [ "style" => "height: 30px; background-color: transparent !important; padding: 0; border: 0;"]) : null;
        return [ "tag" => "form", "attributes" => [ "id" => "form-payments-".$idinvoice, "name" => "form-payments", "action" => "/admin/invoice/".$case, "method" => "post", "class" => "form-table ".self::classStyle($value), "onSubmit" => "return CheckRequiredFieldsInForm(event,['totalPaymentDue','paymentDueDate']);" ], "content" => $content ];
    }
    
    static private function classStyle($value): string {
        if ($value) {
            $expired = null;
            $now = new DateTime();
            try {
                $expired = new DateTime($value['paymentDueDate']);
            } catch (Exception $e) {
            }
            $diff = $expired->diff($now);
        }
        if ($value == null) { 
            return "form-back-gray";
        } elseif ($value['paymentDate'] && $value['paymentDate'] !== "0000-00-00") {
            return "form-back-green";
        } elseif($diff->invert == 0) {
            return "form-back-red";
        } elseif($diff->days < 30) {
            return "form-back-yellow";
        } else { 
            return "form-back-white";             
        }
    }

    protected function balance(): array {
        // SET VARS
        $totalPaymentAmount = OrderItemView::getTotalBill();
        $totalInvoiceAmount = $this->totalInvoiceAmount;
        $totalPaidAmount = $this->totalPaidAmount;
        $totalPayableAmount = $this->totalPayableAmount;
        $totalPastDueAmount = $this->totalPastDueAmount;
        $difference = $totalInvoiceAmount - $totalPaymentAmount;
        $colorDiference = $difference < 0 ? "#fab5b5" : "inherit";
        $colorPayable = $totalPayableAmount > 0 ? "#fafab5" : "inherit";
        $colorPastDue = $totalPastDueAmount > 0 ? "#fab5b5" : "inherit";
        // TABLE
        $table = new Table();
        // CAPTION
        $table->caption(_("Balance"));
        // HEADERS
        $table->head(_("Total order amount") )
            ->head(_("Total invoice amount"))
            ->head(_("Difference"))
            ->head(_("Amounts paid"))
            ->head(_("Amounts payable"))
            ->head(_("Amounts past due"));
        // BODY
        $table->bodyCell(number_format($totalPaymentAmount,2,',','.'), [ "style" => "text-align: center;" ])
            ->bodyCell(number_format($totalInvoiceAmount,2,',','.'), [ "style" => "text-align: center;" ])
            ->bodyCell(number_format($difference,2,',','.'), [ "style" => "text-align: center; color: $colorDiference" ])
            ->bodyCell(number_format($totalPaidAmount,2,',','.'), [ "style" => "text-align: center;" ])
            ->bodyCell(number_format($totalPayableAmount,2,',','.'), [ "style" => "text-align: center; color: $colorPayable" ])
            ->bodyCell(number_format($totalPastDueAmount,2,',','.'), [ "style" => "text-align: center; color: $colorPastDue" ])
            ->closeRow();
        // READY
        return $table->ready();
    }

    /**
     * SALDO
     * @param $data
     * @return array
     */
    protected static function saldoData($data): array {
        $dadosSaldo = [];
        $totalPaymentAmount = 0;
        $dadosSaldo['credito'] = 0;
        $dadosSaldo['debito'] = 0;
        $dadosSaldo['atrasado'] = 0;
        foreach ($data as $value) {
            $paid = $value['paymentDate'] !== "0000-00-00" && $value['paymentDate'] !== null;
            // total
            $totalPaymentAmount += $value['totalPaymentDue'];
            // pago            
            $dadosSaldo['credito'] += $paid ? $value['totalPaymentDue'] : 0;
            // debito
            $dadosSaldo['debito'] += $paid ? null : $value['totalPaymentDue'];
            // atrasado
            $dadosSaldo['atrasado'] += $paid === false && $value['paymentDueDate'] < date("Y-m-d") ? $value['totalPaymentDue'] : null;
        }
        self::$totalPaymentAmount = $totalPaymentAmount;
        return $dadosSaldo;
    }
}
