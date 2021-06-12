<?php
namespace Plinct\Cms\View\Types\Organization;

use Plinct\Cms\View\Types\Intangible\ContactPointView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\Order\OrderView;

class OrganizationView extends OrganizationWidget {

    public function index(array $data): array {
        // NAVBAR
        parent::navbarIndex();
        //
        if (isset($data['errorInfo'])) {
            $this->content['main'][] = self::errorInfo($data['errorInfo'], "organization");
        } else {
            $this->content['main'][] = self::listAll($data, "organization", _("List of organizations"), [ "additionalType" => "Additional type", "dateModified" => "Date Modified" ]);
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
        if (empty($data)) {
            // NAVBAR
            parent::navbarIndex();
            $this->content['main'][] = parent::noContent(_("No item founded!"));
        } else {
            $value = parent::setValues($data[0]);
            // NAVBAR
            parent::navbarEdit();
            // organization
            $this->content['main'][] = self::divBox2(sprintf(_("Edit %s"), _("organization")), [self::formOrganization('edit', $value)]);
            // location
            $this->content['main'][] = self::divBoxExpanding(_("Place"), "Place", [self::relationshipOneToOne("organization", $this->id, "location", "place", $value['location'])]);
            // contact point
            $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [(new ContactPointView())->getForm('organization', $this->id, $value['contactPoint'])]);
            // member
            $this->content['main'][] = self::divBoxExpanding(_("Persons"), "Person", [self::relationshipOneToMany("organization", $this->id, "person", $value['member'])]);
            // image
            $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [(new ImageObjectView())->getForm("organization", $this->id, $value['image'])]);
        }
        return $this->content;
    }

    public function service($data): array {
        parent::setValues($data[0]);
        // NAVBAR
        parent::navbarEdit();
        return $this->itemView("Service", $data);
    }
    public function product($data): array {
        parent::setValues($data[0]);
        // NAVBAR
        parent::navbarEdit();
        return $this->itemView("Product", $data);
    }

    /**
     * ORDER
     * @param $data
     * @return array
     */
    public function order($data): array {
        // NAVBAR ORGANIZATION
        if ($data[0]['@type'] == "Organization") {
            parent::setValues($data[0]);
        } else {
            parent::setValues($data[0]['seller']);
        }
        parent::navbarEdit();
        // VARS
        $action = filter_input(INPUT_GET, 'action');
        // PAYMENT
        if ($action == "payment") {
            $itemResponse = (new OrderView())->payment($data[0]);
            $this->content['navbar'] = array_merge($this->content['navbar'], $itemResponse['navbar']);
            $this->content['main'] = $itemResponse['main'];
            return $this->content;
        }
        // EXPIRE
        if ($action == "expired") {
            $itemResponse = (new OrderView())->expired($data[0]);
            $this->content['navbar'] = array_merge($this->content['navbar'], $itemResponse['navbar']);
            $this->content['main'] = $itemResponse['main'];
            return $this->content;
        }

        // RESPONSE
        return $this->itemView("Order", $data);
    }
}
