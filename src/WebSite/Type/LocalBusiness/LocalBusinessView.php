<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\LocalBusiness;

use Exception;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\ContactPointView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;

class LocalBusinessView
{
    /**
     *
     */
    public function navbarLocalBussines(string $title = null)
    {
        View::navbar(_("Locals business"), [
            "/admin/localBusiness" => Fragment::icon()->home(),
            "/admin/localBusiness/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'localBusiness']);

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
        View::main(Fragment::box()->simpleBox(self::formLocalBussiness(), _("Localbusiness")));
    }

    /**
     * @throws Exception
     */
    public function edit($data)
    {
        $value = $data[0];
        $id = ArrayTool::searchByValue($value['identifier'], "id",'value');

        $this->navbarLocalBussines($value['name']);

        // LOCAL BUSINESS
        $content[] = Fragment::box()->simpleBox(self::formLocalBussiness("edit", $value), _("LocalBusiness"));
        // LOCATION
        $content[] = Fragment::box()->expandingBox(_("Place"), Fragment::form()->relationshipOneToOne("localBusiness", $id, "location", "place", $value['location']));
        // CONTACT POINT
        $content[] = Fragment::box()->expandingBox(_("Contact point"), (new ContactPointView())->getForm("localBusiness", $id, $value['contactPoint']));
        // ORGANIZATION
        $content[] = Fragment::box()->expandingBox(_("Organization"), Fragment::form()->relationshipOneToOne("localBusiness", $id, "organization", "organization", $value['organization']));
        // MEMBER
        $content[] = Fragment::box()->expandingBox(_("Persons"), Fragment::form()->relationshipOneToMany("localBusiness", $id, "person", $value['member']));
        // IMAGE
        $content[] = Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("localBusiness", (int) $id, $value['image']));

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

        $form = Fragment::form(["id"=>"form-localBusiness", "class" => "formPadrao form-localBusiness"]);
        $form->action("/admin/localBusiness/$case")->method('post');
        // hiddens
        if ($case == "edit") $form->input("id", $id, "hidden");
        // name
        $form->fieldsetWithInput("name", $value['name'] ?? null, _("Name"));
        // description
        $form->fieldsetWithTextarea("description", $value['description'] ?? null, _("Description"));
        // disambiguatingDescription
        $form->fieldsetWithTextarea("disambiguatingDescription", $value['disambiguatingDescription'] ?? null, _("Disambiguating description"));
        // hasOfferCatalog
        $form->fieldsetWithInput("hasOfferCatalog", $value['hasOfferCatalog'] ?? null, _("Offer catalog"));
        // url
        $form->fieldsetWithInput("url", $value['url'] ?? null, "Url");
        // dateCreated
        if ($case == "edit") $form->fieldsetWithInput("dateCreated", $value['dateCreated'] ?? null, _("Date created"), "datetime", null, [ "disabled" ]);
        // dateModified
        if ($case == "edit") $form->fieldsetWithInput("dateModified", $value['dateModified'] ?? null, _("Date modified"), "datetime", null, [ "disabled" ]);
        // submit buttons
        $form->submitButtonSend();
        if ($case == "edit") $form->submitButtonDelete("/admin/localBusiness/erase");
        // ready
        return $form->ready();
    }
}
