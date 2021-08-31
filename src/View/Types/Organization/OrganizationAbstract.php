<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Organization;

use Exception;
use Plinct\Cms\View\Fragment\FormFragment;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\Intangible\Order\OrderView;
use Plinct\Cms\View\Types\Intangible\Service\ServiceView;
use Plinct\Cms\View\Types\Product\ProductView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

abstract class OrganizationAbstract
{
    /**
     * @var array
     */
    protected array $content = [];
    /**
     * @var int
     */
    protected int $id;
    /**
     * @var string
     */
    protected string $name;

    use FormElementsTrait;

    /**
     * @param array $value
     * @return array
     */
    protected function setValues(array $value): array
    {
        $organization = $value['@type'] == 'Organization' ? $value : $value['provider'];
        $this->id = (int)ArrayTool::searchByValue($organization['identifier'], "id",'value');
        $this->name = $organization['name'];
        return $value;
    }

    /**
     * @param string $target
     * @param $content
     */
    protected function addContent(string $target, $content)
    {
        $this->content[$target][] = $content;
    }

    /**
     * INDEX NAVBAR
     */
    protected function navbarIndex()
    {
        View::navbar(_("Organization"), [
            "/admin/organization"=> Fragment::icon()->home(),
            "/admin/organization/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'organization']);
    }

    /**
     *
     */
    protected function navbarNew()
    {
        $this->navbarIndex();
        View::navbar(_("Add new"), [], 3);
    }

    /**
     *
     */
    protected function navbarEdit()
    {
        $this->navbarIndex();

        View::navbar($this->name, [
            "/admin/organization/edit?id=$this->id" => Fragment::icon()->home(),
            "/admin/organization?id=$this->id&action=service" => _("Services"),
            "/admin/organization/product?id=$this->id" => _("Products"),
            "/admin/organization/order?id=$this->id" => _("Orders")
        ], 3);
    }

    /**
     * FORM EDIT AND NEW
     * @param string $case
     * @param null $value
     * @return array
     */
    protected function formOrganization(string $case = 'new', $value = null): array
    {
        $content[] = [ "tag" => "h3", "content" => $value['name'] ?? null ];
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null);
        // ADDITIONAL TYPE
        $content[] = self::fieldset(FormFragment::selectAdditionalType('organization',$value['additionalType'] ?? null), _("Additional type"));
        // legal name
        $content[] = self::fieldsetWithInput(_("Legal Name"), "legalName", $value['legalName'] ?? null);
        // tax id
        $content[] = self::fieldsetWithInput(_("Tax Id"), "taxId", $value['taxId'] ?? null);
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null, 100 );
        // disambiguatingDescription
        $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'] ?? null);
        // has offer catalog
        $content[] = self::fieldsetWithInput(_("Has offer catalog"), "hasOfferCatalog", $value['hasOfferCatalog'] ?? null);
        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'] ?? null);
        //submit
        $content[] = self::submitButtonSend();
        if ($case == "edit") {
            $content[] = self::submitButtonDelete('/admin/organization/delete');
        }
        return [ "tag" => "form", "attributes" => [ "name" => "form-organization", "id" => "form-organization", "class" => "formPadrao form-organization", "action" => "/admin/organization/$case", "method" => "post" ], "content" => $content ];
    }

    /**
     * @throws Exception
     */
    protected function itemView(string $itemType, $data): array
    {
        $itemProperty = null;
        $itemResponse = null;
        $itemView = null;
        $value = $data[0];
        $action = filter_input(INPUT_GET, 'action');
        $itemText = lcfirst($itemType)."s";

        // SET ITEM PROPERTY
        if ($itemType == "Product") {
            $itemProperty = "manufacturer";
            $itemView = new ProductView();
        }
        if ($itemType == "Service") {
            $itemProperty = "provider";
            $itemView = new ServiceView();
        }
        if ($itemType == "Order") {
            $itemProperty = "seller";
            $itemView = new OrderView();
        }
        // NO CONTENT
        if (!$itemProperty || !is_object($itemView)) {
            $this->addContent('main', self::noContent("No item detected"));
            return $this->content;
        }
        // NEW
        if ($action == "new") {
            $itemResponse = $itemView->newWithPartOf([ $itemProperty => $value ]);
        }
        // NO CONTENT
        elseif ($value['@type'] == "Organization" && empty($value[$itemText])) {
            $this->addContent('main', [ "tag" => "p", "content" => _("No $itemText added") ]);
        }
        // EDIT
        elseif ($value['@type'] == $itemType) {
            $itemResponse = $itemView->editWithPartOf($value);
        }
        // INDEX
        else {
            $itemResponse = $itemView->indexWithPartOf($value);
        }
        $this->content['main'] = $itemResponse['main'] ?? null;
        // RESPONSE
        return $this->content;
    }
}
