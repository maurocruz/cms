<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class LocalBusinessView {
    protected $content;
    public $localBusinessId = null;
    public $localBusinessName;

    use navbarTrait;
    use FormElementsTrait;
    
    public function navbarLocalBussines() {
        $title = _("Locals business");
        $list = [ "/admin/localBusiness" => _("View all"), "/admin/localBusiness/new" => _("Add new") ];
        $search = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "localBusiness", "data-searchfor" => "name" ] ];
        $this->content['navbar'][] = self::navbar($title, $list, 2, $search);
        if ($this->localBusinessId) {
            $this->content['navbar'][] = self::navbar($this->localBusinessName, [], 3);
        } 
    }

    public function index($data): array {
        $this->navbarLocalBussines();
        $this->content['main'][] = self::listAll($data, "localBusiness", "LocalBusiness list", [ "dateModified" => "Date modified" ]);
        return $this->content;
    }
    
    public function new(): array {
        $this->navbarLocalBussines();
        $this->content['main'][] = self::divBox(_("Localbusiness"), "LocalBusiness", [ self::formLocalBussiness() ]);
        return $this->content;
    }
    
    public function edit($data): array {
        $value = $data[0];
        $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $this->navbarLocalBussines();
        $this->content['main'][] = self::divBox(_("LocalBusiness"), "LocalBusiness", [ self::formLocalBussiness("edit", $value) ]);
        // PLACE
        $this->content['main'][] = self::divBoxExpanding(_("Place"), "Place", [ self::relationshipOneToOne("localBusiness", $id, "location", "place", $value['location']) ]);
        // CONTACT POINT
        $this->content['main'][] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new ContactPointView())->getForm("localBusiness", $id, $value['contactPoint']) ]);
        // ADDRESS
        $this->content['main'][] = self::divBoxExpanding(_("Address"), "PostalAddress", [ (new PostalAddressView())->getForm("localBusiness", $id, $value['address']) ]);
        // ORGANIZATION
        $this->content['main'][] = self::divBoxExpanding(_("Organization"), "Organization", [ self::relationshipOneToOne("localBusiness", $id, "organization", "organization", $value['organization']) ]);
        // PERSON
        $this->content['main'][] = self::divBoxExpanding(_("Persons"), "Person", [ self::relationshipOneToMany("localBusiness", $id, "person", $value['member']) ]);
        // IMAGE
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "imageObject", [ (new ImageObjectView())->getForm("localBusiness", $id, $value['image']) ]);
        return $this->content;
    }

    private static function formLocalBussiness($case = "new", $value = null): array {
        $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $content[] = $case == "edit" ? self::input("id", "hidden", $id) : null;
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null, [ "style" => "width: 50%" ]);
        // additionalType
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'] ?? null, [ "style" => "width: 50%" ]);
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null);
        // disambiguatingDescription
        $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'] ?? null);
        // hasOfferCatalog
        $content[] = self::fieldsetWithInput( _("Offer catalog"), "hasOfferCatalog", $value['hasOfferCatalog'] ?? null, [ "style" => "width: calc(100% - 400px);" ]);
        // dateCreated
        $content[] = $case == "edit" ? self::fieldsetWithInput( _("Date created"), "dateCreated", $value['dateCreated'] ?? null, [ "style" => "width: 200px" ], "datetime", [ "disabled" ]) : null;
        // dateModified
        $content[] = $case == "edit" ?  self::fieldsetWithInput( _("Date modified"), "dateModified", $value['dateModified'] ?? null, [ "style" => "width: 200px" ], "datetime", [ "disabled" ]) : null;
        // url
        $content[] = self::fieldsetWithInput( "url", "url", $value['url'] ?? null, [ "style" => "width: 50%" ]);
        $content[] = self::submitButtonSend();
        if ($case == "edit") {
            $content[] = self::submitButtonDelete("/admin/localBusiness/erase");
        }
        return [ "tag" => "form", "attributes" => [ "id" => "localBusiness-form", "class" => "formPadrao", "action" => "/admin/localBusiness/$case", "method" => "post" ], "content" => $content ];
    }
}
