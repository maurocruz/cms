<?php
namespace Plinct\Cms\View\Types\Organization;

use Plinct\Cms\View\Types\Intangible\Order\OrderView;
use Plinct\Cms\View\Types\Intangible\Service\ServiceView;
use Plinct\Cms\View\Types\Product\ProductView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

abstract class OrganizationWidget {
    protected $content = [];
    protected $id;
    protected $name;
    protected $idItem;
    protected $nameItem;

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
            "/admin/organization/service?id=$this->id" => _("Services"),
            "/admin/organization/product?id=$this->id" => _("Products"),
            "/admin/organization/order?id=$this->id" => _("Orders")
        ];
        $this->addContent('navbar', self::navbar($this->name, $subMenus, 3));
    }

    protected function navbarItem($itemType) {
        // navbar organization
        $this->navbarEdit();
        // navbar subclass
        $dataSearchfor = $itemType == "Order" ? "customer" : "name";
        $this->addContent('navbar', self::navbar(
            _($itemType),
            [ "?id=$this->id" => _("List all"), "?id=$this->id&action=new" => sprintf(_("Add new %s"), _(lcfirst($itemType))) ],
            4,
            [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => lcfirst($itemType), "data-searchfor" => $dataSearchfor ] ]
        ));
        // navbar item subclass
        if ($this->nameItem) {
            $this->addContent('navbar', self::navbar($this->nameItem, [], 5));
        }
    }

    /**
     * FORM EDIT AND NEW
     * @param string $case
     * @param null $value
     * @return array
     */
    protected function formOrganization(string $case = 'new', $value = null): array {
        $content[] = [ "tag" => "h3", "content" => $value['name'] ?? null ];
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];
        }
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null, [ "style" => "width: 50%;" ]);
        // ADDITIONAL TYPE
        $content[] = self::additionalTypeInput("Organization", $case, $value['additionalType'], [ "style" => "width: 50%" ], false);
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

    protected function itemView(string $itemType, $data): array {
        $itemProperty = null;
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
        // CONTINUE SET PROPERTIES
        $owner = $action !== "new" ? ($value[$itemProperty] ?? null) : null;
        $this->id = $owner ? ArrayTool::searchByValue($owner['identifier'], "id")['value'] : ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $this->name = $owner ? $owner['name'] : $value['name'];
        $this->idItem = $owner ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $this->nameItem = $owner ? ($value['name'] ?? null) : null;
        // NAVBAR
        $this->navbarItem($itemType);
        // SWITCH
        if ($action == "new") {
            $this->addContent('main', $itemView->newWithPropertyOf([ $itemProperty => $value ]));
        } elseif ($value['@type'] == "Organization" && empty($value[$itemText])) {
            $this->addContent('main', [ "tag" => "p", "content" => _("No $itemText added") ]);
        } elseif ($value['@type'] == $itemType) {
            $this->addContent('main', $itemView->editWithPropertyOf($value));
        } else {
            $this->addContent('main', $itemView->indexWithPropertyOf($value));
        }
        // RESPONSE
        return $this->content;
    }
}