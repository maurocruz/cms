<?php

declare(strict_types=1);

namespace Plinct\Cms\View\WebSite\Type\Intangible\Invoice;

use DateTime;
use Exception;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\OrderItem\OrderItemView;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

abstract class InvoiceAbstract
{
  /**
   * @var int
   */
  protected int $idorder;
  /**
   * @var string|null
   */
  protected static ?string $tableHasPart = null;
  /**
   * @var string|null
   */
  protected static ?string $idHasPart = null;
  /**
   * @var int
   */
  protected static int $customer;
  /**
   * @var string
   */
  protected static string $customerType;
  /**
   * @var int
   */
  protected static int $provider;
  /**
   * @var string
   */
  protected static string $providerType;
  /**
   * @var float|int
   */
  protected float $totalInvoiceAmount = 0;
  /**
   * @var float|int
   */
  protected float $totalPaidAmount = 0;
  /**
   * @var float|int
   */
  protected float $totalPayableAmount = 0;
  /**
   * @var float|int
   */
  protected float $totalPastDueAmount = 0;

  /**
   * @param string $case
   * @param $value
   * @param $n
   * @return array
   */
  protected function formInvoice(string $case = 'new', $value = null, $n = null): array
  {
    // VARS
    $idinvoice = $value ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;

    $form = CmsFactory::response()->fragment()->form(['id'=>"form-payments-".$idinvoice, "name" => "form-payments", "class" => "form-table form-invoice ".self::classStyle($value), "onSubmit" => "return CheckRequiredFieldsInForm(event,['totalPaymentDue','paymentDueDate']);"]);
    $form->action("/admin/invoice/".$case)->method("post");
    // HIDDENS
    $form->input("referencesOrder", (string)$this->idorder, "hidden");
    $form->input("tableHasPart", (string) $this->idorder, "hidden");
    if ($case == "edit")  $form->input("idinvoice", $idinvoice, "hidden");
    // #
    $p = $case == "new" ? "+" : $n;
    $form->content("<span>".$p."</span>");
    // TOTAL PAYMENT DUE
    $form->fieldsetWithInput("totalPaymentDue",$value['totalPaymentDue'] ?? null, $case == "new" ? _("Value") : null, "number", null, ["type" => "number", "step" => "0.01", "min" => "0.01"]);
    // PAYMENT DUE DATE
    $form->fieldsetWithInput("paymentDueDate", $value['paymentDueDate'] ?? null, $case == "new" ? _("Due date") : null, "date");
    // PAYMENT DATE
    $form->fieldsetWithInput("paymentDate", $value['paymentDate'] ?? null, $case == "new" ? _("Payment") : null, 'date');
    // PAYMENT STATUS
    $form->fieldsetWithSelect("paymentStatus",$value['paymentStatus'] ?? null, [
      "PaymentDue" => _("Payment due"),
      "PaymentComplete" => _("Payment complete"),
      "PaymentPastDue" => _("Payment past due"),
      "PaymentDeclined" => _("Payment declined"),
      "PaymentAutomaticallyApplied" => _("Payment automatically applied")
    ], $case == "new" ? _("Status") : null);
    // SUBMIT
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/invoice/erase");
    // READY
    return $form->ready();
  }

  /**
   * @param $value
   * @return string
   */
  static private function classStyle($value): string
  {
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

  /**
   * @return array
   */
  protected function balance(): array
  {
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
    return ['tag'=>'div','attributes'=>['style'=>'max-width: 100%; overflow-x: scroll;'], 'content'=>$table->ready()];
  }

  /**
   * SALDO
   * @param $data
   * @return array
   */
  protected static function saldoData($data): array
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

    return $dadosSaldo;
  }
}
