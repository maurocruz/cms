<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Invoice;

use DateTime;
use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\OrderItem\OrderItemView;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Tool\ToolBox;
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
   * @var ?string
   */
  protected ?string $customerIdthing = null;
  /**
   * @var string
   */
  protected string $customerType;
	/**
	 * @var string
	 */
	protected string $customerName;
	/**
	 * @var int
	 */
	protected int $customerId;
  /**
   * @var ?string
   */
  protected ?string $providerIdthing = null;
  /**
   * @var string
   */
  protected string $providerType;
	/**
	 * @var mixed|null
	 */
	protected string $providerName;
	/**
	 * @var int|null
	 */
	protected ?int $providerId;
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
	 * @param array $customer
	 */
	public function setCustomer(array $customer): void
	{
		$typeBuilder = ToolBox::typeBuilder($customer);
		$this->customerIdthing = $typeBuilder->getIdthing();
		$this->customerId = $typeBuilder->getId();
		$this->customerType = $typeBuilder->getType();
		$this->customerName = $typeBuilder->getValue('name');
	}

	public function setProvider(array $provider): void
	{
		$typeBuilder = ToolBox::typeBuilder($provider);
		$this->providerIdthing = $typeBuilder->getIdthing();
		$this->providerId = $typeBuilder->getId();
		$this->providerType = $typeBuilder->getType();
		$this->providerName = $typeBuilder->getValue('name');
	}

	public function setOrder(array $order): void
	{
		$typeBuilder = ToolBox::typeBuilder($order);
		$this->idorder = $typeBuilder->getId();
	}

	/**
	 * @param array $provider
	 * @return void
	 */
	public function navbarIndex(array $provider)
	{
		if (!$this->providerIdthing) {
			$this->setProvider($provider);
		}
		if ($this->providerType == 'Organization') {
			Organization::navbarIndex();
			Organization::navbarEdit($this->providerName, $this->providerId, $this->providerIdthing);
		}
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('invoice')
				->title(_('Invoice'))
				->level(5)
				->newTab("/admin/invoice?provider=$this->providerIdthing", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/invoice?provider=$this->providerIdthing&overdueInvoice=true", _('Faturas abertas'))
				->ready()
		);
	}

  /**
   * @param string $case
   * @param $value
   * @param $n
   * @return array
   */
  protected function formInvoice(string $case = 'new', $value = null, $n = null): array
  {
		if ($value) {
			$typeBuilderInvoice = ToolBox::typeBuilder($value);
			$idinvoice = $typeBuilderInvoice->getId();
		} else {
			$idinvoice = null;
		}
		$scheduledPaymentDate = $value['scheduledPaymentDate'] ?? null;
		$paymentDueDate = $value['paymentDueDate'] ?? null;
		$totalPaymentDue = $value['totalPaymentDue'] ?? null;
	  $paymentStatus = $value['paymentStatus'] ?? null;
		// FORM
    $form = CmsFactory::view()->fragment()->form(["name" => "form-payments", "class" => "form-table form-invoice ".self::classStyle($value)]);
    $form->action("/admin/invoice/".$case)->method("post");
		$form->addMandatories('totalPaymentDue','scheduledPaymentDate','paymentStatus')->setIdform("form-payments-".($idinvoice ?? 'new'));
    // HIDDENS
	  $form->input('customer', $this->customerIdthing, 'hidden');
	  $form->input('provider', $this->providerIdthing, 'hidden');
    $form->input("referencesOrder", (string)$this->idorder, "hidden");
    if ($case == "edit")  $form->input("idinvoice", $idinvoice, "hidden");
    // #
    $p = $case == "new" ? "+" : $n;
    $form->content("<span>".$p."</span>");
    // TOTAL PAYMENT DUE
    $form->fieldsetWithInput("totalPaymentDue", $totalPaymentDue, $case == "new" ? _("Value") : null, "number", null, ["type" => "number", "step" => "0.01", "min" => "0.01"]);
    // SCHEDULE PAYMENT DATE
    $form->fieldsetWithInput("scheduledPaymentDate", $scheduledPaymentDate, $case == "new" ? _("Scheduled paydate") : null, "date");
    // PAYMENT DUE DATE
    $form->fieldsetWithInput("paymentDueDate", $paymentDueDate, $case == "new" ? _("Payment") : null, 'date');
    // PAYMENT STATUS
    $form->fieldsetWithSelect("paymentStatus", $paymentStatus, [
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
			$scheduledPaymentDate = $value['scheduledPaymentDate'] ?? null;
			$paymentDueDate = $value['paymentDueDate'] ?? null;
      try {
          $expired = new DateTime($scheduledPaymentDate);
      } catch (Exception $e) {
      }
      $diff = $expired->diff($now);
    }
    if ($value == null) {
      return "form-back-gray";
    } elseif ($paymentDueDate && $paymentDueDate !== "0000-00-00") {
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
    $dadosSaldo['credito'] = 0;
    $dadosSaldo['debito'] = 0;
    $dadosSaldo['atrasado'] = 0;

    foreach ($data as $value) {
      $paid = $value['paymentDate'] !== "0000-00-00" && $value['paymentDate'] !== null;
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
