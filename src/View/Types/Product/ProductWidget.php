<?php
namespace Plinct\Cms\View\Types\Product;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

abstract class ProductWidget {
    protected $content;

    use FormElementsTrait;
    Use navbarTrait;

    protected function formProduct($case = "new", $value = null): array {
        // MANUFACTURER
        $content[] = self::input("manufacturer", "hidden", ArrayTool::searchByValue($value['manufacturer']['identifier'], "id")['value']);
        //$content[] = self::fieldset( self::chooseType("manufacturer","organization,person", $value['manufacturer'] ?? null, "name", [ "style" => "display: flex;"]), _("Manufacturer"), [ "style" => "width: 100%" ]);
        // ID
        $content[] = $case == "edit" ? self::input("id", "hidden", ArrayTool::searchByValue($value['identifier'], "id")['value']) : null;
        // NAME
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null, [ "style" => "width: 100%;"] );
        // CATEGORY
        $content[] = self::fieldsetWithInput(_("Category"), "category", $value['category'] ?? null, [ "style" => "width: 50%;"] );
        // ADDITIONAL TYPE
        $content[] = self::additionalTypeInput("Thing", $case, $value['additionalType'] ?? null, [ "style" => "width: 50%;"] );
        // DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null);
        // DISAMBIGUATING DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'] ?? null);
        // created time
        $content[] =  $case == "edit" ? self::fieldsetWithInput(_("Date created"), "dateCreated", $value['dateCreated'] ?? null, [ "style" => "width: 200px;"], "text", [ "disabled" ] ) : null;
        // update time
        $content[] =  $case == "edit" ? self::fieldsetWithInput(_("Date modified"), "dateModified", $value['dateModified'] ?? null, [ "style" => "width: 200px;"], "text", [ "disabled" ] ) : null;
        // SUBMIT BUTTONS
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/product/erase") : null;
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/product/$case", "method" => "post", "class" => "formPadrao"], "content" => $content ];
    }
}