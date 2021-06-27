<?php
namespace Plinct\Cms\View\Types\Intangible;

use Plinct\Tool\ArrayTool;
use Plinct\Web\Widget\FormTrait;

class PostalAddressView {
    use FormTrait;
    
    public function getForm($tableHasPart, $idHasPart, $value): array {
        return self::formPostalAddress($tableHasPart, $idHasPart, $value ? 'edit' : 'new', $value);
    }
    
    static private function formPostalAddress($tableHasPart, $idHasPart, $case = 'new', $value = null): array {
        $content[] = self::input('tableHasPart', "hidden", $tableHasPart);
        $content[] = $case == "new" ? self::input('idHasPart', "hidden", $idHasPart) : null;
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $content[] = self::input("id", "hidden", $id);
        }
        // streetAddress
        $content[] = self::fieldsetWithInput(_("Street address"), "streetAddress", $value['streetAddress'] ?? null);
        // addressLocality
        $content[] = self::fieldsetWithInput(_("Address locality"), "addressLocality", $value['addressLocality'] ?? null);
        // addressRegion
        $content[] = self::fieldsetWithInput(_("Address region"), "addressRegion", $value['addressRegion'] ?? null);
        // addressCountry
        $content[] = self::fieldsetWithInput(_("Address country"), "addressCountry", $value['addressCountry'] ?? null);
        // postalCode
        $content[] = self::fieldsetWithInput(_("Postal code"), "postalCode", $value['postalCode'] ?? null);
        // submits
        $content[] = self::submitButtonSend();
        $content[] = $case =="edit" ? self::submitButtonDelete("/admin/postalAddress/erase") : null;
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao form-postalAddress", "action" => "/admin/postalAddress/$case", "method" => "post" ], "content" => $content ];
    }
}
