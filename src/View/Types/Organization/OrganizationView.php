<?php
namespace Plinct\Cms\View\Types\Organization;

use Plinct\Cms\View\Types\Intangible\ContactPointView;
use Plinct\Cms\View\Types\Product\ProductView;
use Plinct\Cms\View\Types\Intangible\Service\ServiceView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Tool\ArrayTool;

class OrganizationView extends OrganizationWidget {
    
    public function index(array $data): array {
        // NAVBAR
        parent::navbarIndex();
        //
        if (isset($data['errorInfo'])) {
            $this->content['main'][] = self::errorInfo($data['errorInfo'], "organization");
        } else {
            $this->content['main'][] = self::listAll($data, "organization", _("List of organizations"), [ "dateModified" => "Date Modified" ]);
        }
        return $this->content;
    }
    
    public function new(): array {
        // NAVBAR
        parent::navbarNew();
        //
        $this->content['main'][] = self::divBox(_("Add organization"), "Organization", [ self::formOrganization() ]);
        return $this->content;
    }

    public function edit(array $data): array {
        $value = parent::setValues($data);
        // NAVBAR
        parent::navbarEdit();
        // organization
        $this->content['main'][] = self::divBox2(sprintf(_("Edit %s"), _("organization")), [ self::formOrganization('edit', $value) ]);
        // location
        $this->content['main'][] = self::divBoxExpanding(_("Place"), "Place", [ self::relationshipOneToOne("organization", $this->id, "location", "place", $value['location']) ]);
        // contact point
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new ContactPointView())->getForm('organization', $this->id, $value['contactPoint']) ]);
        // member
        $this->content['main'][] = self::divBoxExpanding(_("Persons"), "Person", [ self::relationshipOneToMany("organization", $this->id, "person", $value['member']) ]);
        // image
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("organization", $this->id, $value['image']) ]);
        return $this->content;
    }

    public function service($data) {
        $value = $this->setValues($data);
        $action = filter_input(INPUT_GET, 'action');
        // NAVBAR
        $this->navbarService();
        // SWICTH
        if ($action == "new") {
            $this->addContent('main', (new ServiceView())->new([ "provider" => $value ]));
        } elseif ($value['@type'] == "Organization" && empty($value['services'])) {
            $this->addContent('main', [ "tag" => "p", "content" => _("No services added") ]);
        } elseif ($value['@type'] = "Service") {
            parent::addContent('main', (new ServiceView())->edit($value));
        } else {
            parent::addContent('main', (new ServiceView())->index($value));
        }
        return $this->content;
    }

    public function product($data) {
        $value = $data[0];
        $organizatiionIdentifier = $value['manufacturer']['identifier'] ?? $value['identifier'];
        $productName = $value['@type'] == "Product" ? $value['name'] : null;
        $this->id =  ArrayTool::searchByValue($organizatiionIdentifier, "id")['value'];
        $this->name = $value['manufacturer']['name'] ?? $value['name'];
        $action = filter_input(INPUT_GET, 'action');
        // NAVBAR
        $this->navbarProduct($productName);
        // SWITCH
        if ($action == "new") {
            $this->addContent('main', (new ProductView())->new([ "manufacturer" => $value ]));
        } elseif ($value['@type'] == "Organization" && empty($value['products'])) {
            $this->addContent('main', [ "tag" => "p", "content" => _("No products added") ]);
        } elseif ($value['@type'] == "Product") {
            parent::addContent('main', (new ProductView())->editWithPropertyOf($value));
        } else {
            parent::addContent('main', (new ProductView())->indexWithPropertyOf($value));
        }
        return $this->content;
    }
}
