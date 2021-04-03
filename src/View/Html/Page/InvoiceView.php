<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class InvoiceView implements ViewInterface {
    private static $tableHasPart = null;
    private static $idHasPart = null;
    private static $customer;
    private static $customerType;
    private static $provider;
    protected static $providerType;
    private $content = [];

    use FormElementsTrait;
    use navbarTrait;

    private function navbarInvoice($title = null, $list = null, $level = 2) {
        $title = $title ?? _("Invoice");
        $list = $list ?? [
            "/admin/invoice" => _("List all"),
            "/admin/invoice/new" => _("Add new")
        ];
        $this->content['navbar'][] = self::navbar($title, $list, $level);
    }

    public function index(array $data): array {
        $this->navbarInvoice();
        $this->content['main'][] = self::listAll($data, "Invoice", null, [ "customer:name" => _("Customer"), "provider:name" => _("Provider"), "paymentDueDate" => _("Payment due date") ] );
        return $this->content;
    }

    public function new($data = null): array {
        $this->navbarInvoice();
        $this->navbarInvoice(_("Add new invoice"), [], 3);
        $this->content['main'][] = self::divBox(_("Add new"), "invoice", [ self::formInvoice() ]);
        return $this->content;
    }

    public function edit(array $data): array {
        $this->navbarInvoice();
        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        }
        else {
            $value = $data[0];
            $this->navbarInvoice(_("Edit invoice"), [], 3);
            $this->content['main'][] = self::divBox(_("Invoice"), "invoice", [ self::formInvoice($value) ]);
        }
        return $this->content;
    }

    public static function getForm($tableHasPart, $idHasPart, $data): array {
        $content[] = PaymentView::edit($data);
        return $content;
    }

    private static function formInvoice($value = null, $n = "+"): array {
        $case = $value ? "edit": "new";
        $content[] = self::$tableHasPart ? self::input("tableHasPart", "hidden", self::$tableHasPart) : null;
        $content[] = self::$idHasPart && $case == "new"  ? self::input("idHasPart", "hidden", self::$idHasPart) : null;
        $content[] = $case == "edit" ? self::input("id", "hidden", ArrayTool::searchByValue($value['identifier'], "id")['value']) : null;
        if (!self::$tableHasPart) {
            // CUSTOMER
            $content[] = self::fieldset(self::chooseType("customer", "organization,person", $value['customer'] ?? null, "name", ["style" => "display: flex;"]), _("Customer"), ["style" => "width: 100%;"]);
            // PROVIDER
            $content[] = self::fieldset(self::chooseType("provider", "organization,person", $value['provider'] ?? null, "name", ["style" => "display: flex;"]), _("Provider"), ["style" => "width: 100%;"]);
        } else {
            $content[] = self::input("customer", "hidden", self::$customer);
            $content[] = self::input("customerType", "hidden", self::$customerType);
            $content[] = self::input("provider", "hidden", self::$provider);
            $content[] = self::input("providerType", "hidden", self::$providerType);
        }
        $content[] = self::$tableHasPart ? $n." " : null;
        // TOTAL PAYMENT DUE
        $content[] = self::fieldsetWithInput(_("Total payment due"), "totalPaymentDue", $value['totalPaymentDue'] ?? null);
        // PAYMENT DUE DATE
        $content[] = self::fieldsetWithInput(_("Payment due date"), "paymentDueDate", $value['paymentDueDate'] ?? null, [], "date");
        // PAYMENT DATE
        $content[] = self::fieldsetWithInput(_("Payment date"), "paymentDate", $value['paymentDate'] ?? null, [], "date");
        // PAYMENT STATUS
        $content[] = self::fieldsetWithSelect(_("Payment status"), "paymentStatus", $value['paymentStatus'] ?? null, [
            "PaymentAutomaticallyApplied" => _("Payment automatically applied"),
            "PaymentComplete" => _("Payment complete"),
            "PaymentDeclined" => _("Payment declined"),
            "PaymentDue" => _("Payment due"),
            "PaymentPastDue" => _("Payment past due")
        ]);
        $content[] = self::submitButtonSend();
        $content[] = $case =="edit" ? self::submitButtonDelete("/admin/invoice/erase") : null;
        return self::form("/admin/invoice/$case", $content);
    }
}
