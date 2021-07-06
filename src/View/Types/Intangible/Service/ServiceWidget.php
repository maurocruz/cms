<?php
namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

abstract class ServiceWidget {
    protected $content;
    protected $tableHasPart;
    protected $idHasPart;

    use FormElementsTrait;

    protected function serviceForm($case = "new", $value = null): array {
        // PROVIDER
        $content[] = self::input("provider", "hidden", $this->idHasPart);
        $content[] = self::input("providerType", "hidden", $this->tableHasPart);
        // ID
        $content[] = $case == "edit" ? self::input("id", "hidden", ArrayTool::searchByValue($value['identifier'], "id")['value']) : null;
        // NAME
        $content[] = self::fieldsetWithInput(_("name"), "name", $value['name'] ?? null);
        // CATEGORY
        $content[] = self::fieldsetWithInput(_("Category"), "category", $value['category'] ?? null );
        // ADDITIONAL TYPE
        $content[] = self::additionalTypeInput("Service", $value['additionalType'] ?? null);
        // SERVICE TYPE
        $content[] = self::fieldsetWithInput(_("Service type"), "serviceType", $value['serviceType'] ?? null);
        // DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null);
        // DISAMBIGUATING DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'] ?? null);
        // TERMS OF SERVICE
        $content[] = self::fieldsetWithTextarea(_("Terms of service"), "termsOfService", $value['termsOfService'] ?? null);
        // created time
        $content[] =  $case == "edit" ? self::fieldsetWithInput(_("Date created"), "dateCreated", $value['dateCreated'] ?? null, null, "text", [ "disabled" ] ) : null;
        // update time
        $content[] =  $case == "edit" ? self::fieldsetWithInput(_("Date modified"), "dateModified", $value['dateModified'] ?? null, null, "text", [ "disabled" ] ) : null;
        // SUBMIT BUTTONS
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/service/erase") : null;
        return self::form("/admin/service/$case", $content);
    }
}