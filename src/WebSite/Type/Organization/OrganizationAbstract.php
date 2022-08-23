<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\Response\View\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\View;
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
        $form = Fragment::form(["name" => "form-organization", "id" => "form-organization", "class" => "formPadrao form-organization"]);
        $form->action("/admin/organization/$case")->method("post");
        $form->content([ "tag" => "h3", "content" => $value['name'] ?? null ]);
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $form->input("id", $id, 'hidden');
        }
        // name
        $form->fieldsetWithInput("name", $value['name'] ?? null, _("Name"));
        // ADDITIONAL TYPE
        $form->fieldset(Fragment::form()->selectAdditionalType('organization',$value['additionalType'] ?? null), _("Additional type"));
        // legal name
        $form->fieldsetWithInput("legalName", $value['legalName'] ?? null, _("Legal Name"));
        // tax id
        $form->fieldsetWithInput("taxId", $value['taxId'] ?? null, _("Tax Id"));
        // description
        $form->fieldsetWithTextarea("description", $value['description'] ?? null, _("Description"));
        // disambiguatingDescription
        $form->fieldsetWithTextarea("disambiguatingDescription", $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
        // has offer catalog
        $form->fieldsetWithInput("hasOfferCatalog", $value['hasOfferCatalog'] ?? null, _("Has offer catalog"));
        // url
        $form->fieldsetWithInput("url", $value['url'] ?? null, "Url");
        //submit
        $form->submitButtonSend();
        if ($case == "edit") {
            $form->submitButtonDelete('/admin/organization/delete');
        }
        // READY
        return $form->ready();
    }

    /**
     * @throws Exception
     */
    /*protected function itemView(string $itemType, $data): array
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
            $this->addContent('main', Fragment::noContent("No item detected"));
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
    }*/
}
