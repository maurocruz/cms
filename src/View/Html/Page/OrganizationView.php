<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class OrganizationView {
    protected $content;
    protected $organizationId = null;
    protected $organizationName;

    use navbarTrait;
    use FormElementsTrait;
    
    public function navbarOrganization() {
        $title = _("Organization");
        $list = [
            "/admin/organization" => _("View all"),
            "/admin/organization/new" => _("Add new organization")
        ];
        $search = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "organization", "data-searchfor" => "name" ] ];
        $this->content['navbar'][] = self::navbar($title, $list, 2, $search);
        if ($this->organizationId) {
            $this->content['navbar'][] = self::navbar($this->organizationName, [ "/admin/service/provider/".$this->organizationId => _("Services") ], 3);
        }
    }
    
    public function index(array $data): array {
        $this->navbarOrganization();
        if (isset($data['errorInfo'])) {
            $this->content['main'][] = self::errorInfo($data['errorInfo'], "organization");
        } else {
            $this->content['main'][] = self::listAll($data, "organization", _("List of organizations"), [ "update_time" => "Update date" ]);
        }
        return $this->content;
    }
    
    public function new(): array {
        $this->navbarOrganization();
        $this->content['main'][] = self::divBox(_("Add organization"), "Organization", [ self::formOrganization() ]);
        return $this->content;
    }

    public function edit(array $data): array {
        $value = $data[0];
        $this->organizationId = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $this->organizationName = $value['name'];
        $this->navbarOrganization();
        // organization
        $this->content['main'][] = self::formOrganization('edit', $value);
        // address
        $this->content['main'][] = self::divBoxExpanding(_("Postal address"), "PostalAddress", [ (new PostalAddressView())->getForm("organization", $this->organizationId, $value['address']) ]);
        // contact point
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new ContactPointView())->getForm('organization', $this->organizationId, $value['contactPoint']) ]);
        // member
        $this->content['main'][] = self::divBoxExpanding(_("Persons"), "Person", [ self::relationshipOneToMany("organization", $this->organizationId, "person", $value['member']) ]);
        // location
        $this->content['main'][] = self::divBoxExpanding(_("Place"), "Place", [ self::relationshipOneToOne("organization", $this->organizationId, "location", "place", $value['location']) ]);
        // areaServed
        //$this->content['main'][] = self::divBoxExpanding(_("Area served"), "Place", [ (new PlaceView())->getForm("organization", $this->organizationId, $value['location']) ]);
        // image
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("organization", $this->organizationId, $value['image']) ]);
        return $this->content;
    }
    
    static private function formOrganization($case = 'new', $value = null): array {
        $content[] = [ "tag" => "h3", "content" => $value['name'] ?? null ];
        if ($case == "edit") {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $content[] = [ "tag" => "input", "attributes" => [ "name" => "id", "type" => "hidden", "value" => $id ] ];            
        }
        // legal name
        $content[] = self::fieldsetWithInput(_("Legal Name"), "legalName", $value['legalName'] ?? null, [ "style" => "width: 32%;" ]);
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null, [ "style" => "width: 32%;" ]);
        // tax id
        $content[] = self::fieldsetWithInput(_("Tax Id"), "taxId", $value['taxId'] ?? null, [ "style" => "width: 32%;" ]);
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null, 100 );
        // additional type
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'] ?? null, [ "style" => "min-width: 320px; width: 30%;" ]);
        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'] ?? null, [ "style" => "width: 30%;" ]);
        //submit
        $content[] = self::submitButtonSend();
        if ($case == "edit") {
            $content[] = self::submitButtonDelete('/admin/organization/delete');
        }
        return [ "tag" => "form", "attributes" => [ "name" => "form-organization", "id" => "form-organization", "class" => "formPadrao box", "action" => "/admin/organization/$case", "method" => "post" ], "content" => $content ];
    }
}
