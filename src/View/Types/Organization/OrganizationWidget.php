<?php

namespace Plinct\Cms\View\Types\Organization;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

abstract class OrganizationWidget {
    protected $content;
    protected $id;
    protected $name;

    use navbarTrait;
    use FormElementsTrait;

    protected function setValues(array $data): array {
        $value = $data[0];
        // ID
        $this->id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        // NAME
        $this->name = $value['name'];
        return $value;
    }

    protected function addContent(string $target, $content) {
        $this->content[$target][] = $content;
    }

    /**
     * INDEX NAVBAR
     */
    protected function navbarIndex() {
        $title = _("Organization");
        $list = [ "/admin/organization" => _("View all"), "/admin/organization/new" => sprintf(_("Add new %s"), _("organization")) ];
        $search = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "organization", "data-searchfor" => "name" ] ];
        $this->addContent('navbar', self::navbar($title, $list, 2, $search));
    }

    protected function navbarNew() {
        $this->navbarIndex();
        $this->addContent('navbar', self::navbar(_("Add new"), [], 3));
    }

    protected function navbarEdit() {
        $this->navbarIndex();
        $subMenus = [
            "/admin/organization/edit?id=$this->id" => _("View it"),
            "/admin/organization/service?id=$this->id" => _("Service"),
            "/admin/organization/product?id=$this->id" => _("Product"),
            "/admin/organization/offer?id=$this->id" => _("Offer")
        ];
        $this->addContent('navbar', self::navbar($this->name, $subMenus, 3));
    }

    protected function navbarService() {
        $this->navbarEdit();
        $this->addContent('navbar', self::navbar(_("Services"), [ "?id=$this->id" => _("List all"), "?id=$this->id&action=new" => sprintf(_("Add new %s"), _("service")) ], 4));
    }

    protected function navbarProduct($name = null) {
        $this->navbarEdit();
        $this->addContent('navbar', self::navbar(_("Products"), [ "?id=$this->id" => _("List all"), "?id=$this->id&action=new" => sprintf(_("Add new %s"), _("product")) ], 4));
        if ($name) {
            $this->addContent('navbar', self::navbar($name, [], 5));
        }
    }

    /**
     * FORM EDIT AND NEW
     * @param string $case
     * @param null $value
     * @return array
     */
    protected function formOrganization($case = 'new', $value = null): array {
        $content[] = [ "tag" => "h3", "content" => $value['name'] ?? null ];
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null, [ "style" => "width: 50%;" ]);
        // ADDITIONAL TYPE
        $content[] = self::additionalTypeInput("Organization", $case, $value['additionalType'], [ "style" => "width: 50%" ]);
        // legal name
        $content[] = self::fieldsetWithInput(_("Legal Name"), "legalName", $value['legalName'] ?? null, [ "style" => "width: 50%;" ]);
        // tax id
        $content[] = self::fieldsetWithInput(_("Tax Id"), "taxId", $value['taxId'] ?? null, [ "style" => "width: 50%;" ]);
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null, 100 );
        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'] ?? null, [ "style" => "width: 50%;" ]);
        //submit
        $content[] = self::submitButtonSend();
        if ($case == "edit") {
            $content[] = self::submitButtonDelete('/admin/organization/delete');
        }
        return [ "tag" => "form", "attributes" => [ "name" => "form-organization", "id" => "form-organization", "class" => "formPadrao", "action" => "/admin/organization/$case", "method" => "post" ], "content" => $content ];
    }
}