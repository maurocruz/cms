<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\LocalBusiness;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\ContactPointView;
use Plinct\Cms\View\Types\Intangible\PostalAddressView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class LocalBusinessView
{
    protected $content;
    public $localBusinessId = null;
    public $localBusinessName;

    use FormElementsTrait;

    /**
     *
     */
    public function navbarLocalBussines(string $title = null)
    {
        View::navbar(_("Locals business"), [
            "/admin/localBusiness" => Fragment::icon()->home(),
            "/admin/localBusiness/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'locaBusiness']);

        if ($title) View::navbar($title, [], 3);
    }

    /**
     * @param $data
     */
    public function index($data)
    {
        $this->navbarLocalBussines();

        $listTable = Fragment::listTable();
        $listTable->caption(sprintf(_("List of %s"), "Local business"));
        $listTable->labels(_('Name'), _("Additional type"), _("Date modified"));
        $listTable->rows($data['itemListElement'],['name','additionalType','dateModified']);
        $listTable->setEditButton("/admin/localBusiness?id=");
        View::main($listTable->ready());
    }

    /**
     *
     */
    public function new()
    {
        $this->navbarLocalBussines();
        View::main(self::divBox(_("Localbusiness"), "LocalBusiness", [ self::formLocalBussiness() ]));
    }

    /**
     * @throws Exception
     */
    public function edit($data)
    {
        $value = $data[0];
        $id = (int)ArrayTool::searchByValue($value['identifier'], "id",'value');

        $this->navbarLocalBussines($value['name']);

        $content[] = self::divBox(_("LocalBusiness"), "LocalBusiness", [ self::formLocalBussiness("edit", $value) ]);
        // PLACE
        $content[] = self::divBoxExpanding(_("Place"), "Place", [ self::relationshipOneToOne("localBusiness", $id, "location", "place", $value['location']) ]);
        // CONTACT POINT
        $content[] = self::divBoxExpanding(_("Contact point"), "ContactPoint", [ (new ContactPointView())->getForm("localBusiness", $id, $value['contactPoint']) ]);
        // ADDRESS
        $content[] = self::divBoxExpanding(_("Address"), "PostalAddress", [ (new PostalAddressView())->getForm("localBusiness", $id, $value['address']) ]);
        // ORGANIZATION
        $content[] = self::divBoxExpanding(_("Organization"), "Organization", [ self::relationshipOneToOne("localBusiness", $id, "organization", "organization", $value['organization']) ]);
        // PERSON
        $content[] = self::divBoxExpanding(_("Persons"), "Person", [ self::relationshipOneToMany("localBusiness", $id, "person", $value['member']) ]);
        // IMAGE
        $content[] = self::divBoxExpanding(_("Images"), "imageObject", [ (new ImageObjectView())->getForm("localBusiness", $id, $value['image']) ]);

        View::main($content);
    }

    /**
     * @param string $case
     * @param null $value
     * @return array
     */
    private static function formLocalBussiness(string $case = "new", $value = null): array
    {
        $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $content[] = $case == "edit" ? self::input("id", "hidden", $id) : null;
        // name
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null);
        // description
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description'] ?? null);
        // disambiguatingDescription
        $content[] = self::fieldsetWithTextarea(_("Disambiguating description"), "disambiguatingDescription", $value['disambiguatingDescription'] ?? null);
        // hasOfferCatalog
        $content[] = self::fieldsetWithInput( _("Offer catalog"), "hasOfferCatalog", $value['hasOfferCatalog'] ?? null);
        // dateCreated
        $content[] = $case == "edit" ? self::fieldsetWithInput( _("Date created"), "dateCreated", $value['dateCreated'] ?? null, null, "datetime", [ "disabled" ]) : null;
        // dateModified
        $content[] = $case == "edit" ?  self::fieldsetWithInput( _("Date modified"), "dateModified", $value['dateModified'] ?? null, null, "datetime", [ "disabled" ]) : null;
        // url
        $content[] = self::fieldsetWithInput( "url", "url", $value['url'] ?? null);
        $content[] = self::submitButtonSend();
        if ($case == "edit") {
            $content[] = self::submitButtonDelete("/admin/localBusiness/erase");
        }
        return [ "tag" => "form", "attributes" => [ "id" => "localBusiness-form", "class" => "formPadrao", "action" => "/admin/localBusiness/$case", "method" => "post" ], "content" => $content ];
    }
}
