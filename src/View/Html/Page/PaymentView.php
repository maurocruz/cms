<?php

namespace Plinct\Cms\View\Html\Page;

use DateTime;
use Exception;
use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;

class PaymentView
{
    private static $totalPaymentAmount;

    use FormElementsTrait;

    public static function edit(array $data): array
    {
        $idorder = $data['idorder'];

        $lenght = count($data['partOfInvoice']);
        $content[] = self::formPayment($idorder, "new", null, $lenght+1 );
        
        if ($lenght > 0) {
            foreach ($data['partOfInvoice'] as $key => $value) {
                $content[] = self::formPayment($idorder, 'edit', $value, $lenght - $key);
            }
            // balance
            $dadosSaldo = self::saldoData($data['partOfInvoice']);
            $content[] = self::balance($dadosSaldo);
        }
        return $content;
    }
    
    private static function formPayment($idorder, $case = 'new', $value = null, $n = null): array
    {
        $idinvoice = $value ? PropertyValue::extractValue($value['identifier'], "id") : null;

        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "value" => "order", "type" => "hidden"] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "referencesOrder", "value" => $idorder, "type" => "hidden"] ];

        if ($case == "edit") {
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "value" => $idinvoice, "type" => "hidden"] ];
        }   
                    
        // #
        $p = $case == "new" ? "+" : $n;
        $content[] = "<span style=\"display: inline-block; width: 30px;\">".$p.".</span>";
        // TOTAL PAYMENT DUE
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 135px; display: inline-block;"], "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Total payment due") ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "totalPaymentDue", "value" => $value['totalPaymentDue'], "type" => "number", "step" => "0.01", "min" => "0.01", "style" => "text-align: right; width: inherit;" ]]
        ]];
        // Payment due date
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 145px; display: inline-block;"], "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Payment due date") ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "paymentDueDate", "value" => $value['paymentDueDate'], "type" => "date", "style" => "width: inherit;" ]]
        ]];
        // Quitado em
        $content[] = [ "tag" => "fieldset", "content" => [ 
            $case == "new" ? [ "tag" => "legend", "content" => _("Payment date") ] : null,
            [ "tag" => "input", "attributes" => [ "name" => "paymentDate", "value" => $value['paymentDate'], "type" => "date" ]]
        ]];
        // PAYMENT STATUS
        $content[] = [ "tag" => "fieldset", "content" => [
            $case == "new" ? [ "tag" => "legend", "content" => _("Payment status") ] : null,
            self::select("paymentStatus", $value['paymentStatus'], [
                "PaymentAutomaticallyApplied" => _("Payment automatically applied"),
                "PaymentComplete" => _("Payment complete"),
                "PaymentDeclined" => _("Payment declined"),
                "PaymentDue" => _("Payment due"),
                "PaymentPastDue" => _("Payment past due")
            ])
        ]];
        // submit
        $content[] = self::submitButtonSend([ "style" => "height: 30px; background-color: transparent !important; padding: 0 5px; border: 0;"]);
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/invoice/erase", [ "style" => "height: 30px; background-color: transparent !important; padding: 0; border: 0;"]) : null;
        
        return [ "tag" => "form", "attributes" => [ "id" => "form-payments-".$idinvoice, "name" => "form-payments", "action" => "/admin/invoice/".$case, "method" => "post", "class" => "form-table ".self::classStyle($value), "onSubmit" => "return CheckRequiredFieldsInForm(event,['totalPaymentDue','paymentDueDate']);" ], "content" => $content ];
    }
    
    static private function classStyle($value): string
    {
        $expired = null;
        $now = new DateTime();

        try {
            $expired = new DateTime($value['paymentDueDate']);
        } catch (Exception $e) {
        }
        $diff = $expired->diff($now);
                
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

    private static function balance($dadosSaldo): array
    {
        $totalWithoutDiscount = OrderItemView::$totalWithoutDiscount;
        $totalWithDiscount = OrderItemView::$totalWithDiscount;
        if($totalWithDiscount != self::$totalPaymentAmount) {
            $diffWithoutDiscount = self::$totalPaymentAmount - $totalWithoutDiscount;
            $diffWithDiscount = self::$totalPaymentAmount - $totalWithDiscount;
            $content[] = [ "tag" => "tr", "attributes" => [ "style" => "background-color: #e7e7e7; color: red;" ], "content" => [
                [ "tag" => "td", "attributes" => [ "colspan" => "4" ], "content" => sprintf(_("Order total does not match invoice total. Differences: without discount %s; with discount %s"), number_format($diffWithoutDiscount,2,',','.'), number_format($diffWithDiscount,2,',','.')) ]
            ]];
        }
        
        $content[] = [
            [ "tag" => "tr", "attributes" => [ "style" => "background-color: #e7e7e7;" ], "content" => [
                [ "tag" => "td", "attributes" => [ "style" => "color: blue;" ], "content" => number_format(self::$totalPaymentAmount,2,",",".") ],
                [ "tag" => "td", "attributes" => [ "style" => "color: blue;" ], "content" => number_format($dadosSaldo['credito'],2,",",".") ],
                [ "tag" => "td", "attributes" => [ "style" => "color: #333" ], "content" => number_format($dadosSaldo['debito'],2,",",".") ],
                [ "tag" => "td", "attributes" => [ "style" => "color: red;" ], "content" => "(".number_format($dadosSaldo['atrasado'],2,",",".").")" ]
            ] ]
        ];

        return [ "tag" => "table", "attributes" => [ "style" => "margin-top: 10px; text-align: center; font-weight: bold;" ], "content" => [
            [ "tag" => "caption", "attributes" => [ "style" => "font-size: 1em;" ], "content" => _("Balance") ],
            [ "tag" => "thead", "content" => [
                [ "tag" => "tr", "content" => [
                    [ "tag" => "th", "content" => _("Total order amount") ],
                    [ "tag" => "th", "content" => _("Amounts paid") ],
                    [ "tag" => "th", "content" => _("Amounts payable") ],
                    [ "tag" => "th", "content" => _("Amounts past due") ]
                ]]
            ]],
            [ "tag" => "tbody", "content" => $content ]
        ]];
    }
    
    // SALDO
    private static function saldoData($data): array
    {
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
