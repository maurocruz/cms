<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Tool\ArrayTool;

class PostalAddressView
{
    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param $value
     * @return array
     */
    public function getForm($tableHasPart, $idHasPart, $value): array
    {
        return self::formPostalAddress($tableHasPart, (string) $idHasPart, $value ? 'edit' : 'new', $value);
    }

    /**
     * @param string $tableHasPart
     * @param string $idHasPart
     * @param string $case
     * @param null $value
     * @return array
     */
    static private function formPostalAddress(string $tableHasPart, string $idHasPart, string $case = 'new', $value = null): array
    {
        $form = Fragment::form(["class" => "formPadrao form-postalAddress"])->action("/admin/postalAddress/$case")->method("post");
        // hiddens
        $form->input('tableHasPart', $tableHasPart, "hidden");
        if ($case == "new") $form->input('idHasPart', $idHasPart, "hidden");
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], 'id')['value'];
            $form->input("id", $id, "hidden");
        }
        // streetAddress
        $form->fieldsetWithInput("streetAddress", $value['streetAddress'] ?? null, _("Street address"));
        // addressLocality
        $form->fieldsetWithInput("addressLocality", $value['addressLocality'] ?? null, _("Address locality"));
        // addressRegion
        $form->fieldsetWithInput("addressRegion", $value['addressRegion'] ?? null, _("Address region"));
        // addressCountry
        $form->fieldsetWithInput("addressCountry", $value['addressCountry'] ?? null, _("Address country"));
        // postalCode
        $form->fieldsetWithInput("postalCode", $value['postalCode'] ?? null, _("Postal code"));
        // submits
        $form->submitButtonSend();
        if ($case =="edit") $form->submitButtonDelete("/admin/postalAddress/erase");
        // ready
        return $form->ready();
    }
}
