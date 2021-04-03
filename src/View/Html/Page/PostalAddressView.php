<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Tool\ArrayTool;
use Plinct\Web\Widget\FormTrait;

class PostalAddressView {
    use FormTrait;
    
    public function getForm($tableHasPart, $idHasPart, $value): array {
        return self::formPostalAddress($tableHasPart, $idHasPart, $value ? 'edit' : 'new', $value);
    }
    
    static private function formPostalAddress($tableHasPart, $idHasPart, $case = 'new', $value = null): array {
        $content[] = $case == "new" ? self::input('tableHasPart', "hidden", $tableHasPart) : null;
        $content[] = $case == "new" ? self::input('idHasPart', "hidden", $idHasPart) : null;
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $content[] = self::input("id", "hidden", $id);
        }
        // streetAddress
        $content[] = self::fieldsetWithInput(_("Street address"), "streetAddress", $value['streetAddress'] ?? null, [ "style" => "width: calc(100% - 650px)"]);
        // addressLocality
        $content[] = self::fieldsetWithInput(_("Address locality"), "addressLocality", $value['addressLocality'] ?? null, [ "style" => "width: 200px;"]);
        // addressRegion
        $content[] = self::fieldsetWithInput(_("Address region"), "addressRegion", $value['addressRegion'] ?? null, [ "style" => "width: 120px;"]);
        // addressCountry
        $content[] = self::fieldsetWithInput(_("Address country"), "addressCountry", $value['addressCountry'] ?? null, [ "style" => "width: 120px;"]);
        // postalCode
        $content[] = self::fieldsetWithInput(_("Postal code"), "postalCode", $value['postalCode'] ?? null, [ "style" => "width: 100px;"]);
        // submits
        $content[] = self::submitButtonSend();
        $content[] = $case =="edit" ? self::submitButtonDelete("/admin/postalAddress/erase") : null;
        // form
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/postalAddress/$case", "method" => "post" ], "content" => $content ];
    }
}
