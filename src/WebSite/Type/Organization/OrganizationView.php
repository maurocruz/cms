<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Organization;

use Exception;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\Intangible\Service\ServiceView;
use Plinct\Cms\WebSite\Type\Structure\Main\MainView;
use Plinct\Cms\WebSite\Type\Intangible\ContactPointView;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\Order\OrderView;
use Plinct\Cms\WebSite\Type\Product\ProductView;
use Plinct\Cms\WebSite\Type\View;

class OrganizationView extends OrganizationAbstract
{
    /**
     * @param array $data
     */
    public function index(array $data)
    {
        // NAVBAR
        parent::navbarIndex();

        $listTable = Fragment::listTable()
            ->caption(_("List of organizations"))
            ->labels(_('Name'),_('Additional type'), _("Date modified"))
            ->rows($data['itemListElement'],['name','additionalType','dateModified'])
            ->setEditButton("/admin/organization/edit/");

        View::main($listTable->ready());
    }

    /**
     * @param
     */
    public function new()
    {
        // NAVBAR
        parent::navbarNew();
        //
        View::main(Fragment::box()->simpleBox( self::formOrganization(), _("Add organization")));
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function edit(array $data)
    {
        if (empty($data)) {
            // NAVBAR
            parent::navbarIndex();
            MainView::content(Fragment::noContent(_("No item founded!")));
        } else {
            $value = parent::setValues($data[0]);
            // NAVBAR
            parent::navbarEdit();
            // organization
            View::main(Fragment::box()->simpleBox(self::formOrganization('edit', $value), sprintf(_("Edit %s"), _("organization"))));
            // location
            View::main(Fragment::box()->expandingBox(_("Place"), Fragment::form()->relationshipOneToOne("organization", (string) $this->id, "location", "place", $value['location'])));
            // contact point
            View::main(Fragment::box()->expandingBox(_("Contact point"), (new ContactPointView())->getForm('organization', $this->id, $value['contactPoint'])));
            // member
            View::main(Fragment::box()->expandingBox(_("Persons"), Fragment::form()->relationshipOneToMany("organization", (string) $this->id, "person", $value['member'])));
            // image
            View::main(Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("organization", $this->id, $value['image'])));
        }
    }

    /**
     * @throws Exception
     */
    public function service($value)
    {
        $action = $value['action'] ?? filter_input(INPUT_GET, 'action');
        $item = filter_input(INPUT_GET, 'item');

        parent::setValues($value);
        // NAVBAR
        parent::navbarEdit();

        $service = new ServiceView();
        if ($action == 'new') {
            $service->newWithPartOf($value);
        } elseif ($action == 'edit' || $item) {
            $service->editWithPartOf($value);
        } else {
            $service->indexWithPartOf($value);
        }
    }

    /**
     * @param $value
     * @throws Exception
     */
    public function product($value)
    {
        $action = $value['action'] ?? filter_input(INPUT_GET, 'action');

        parent::setValues($value);
        // NAVBAR
        parent::navbarEdit();

        // MAIN
        $product = new ProductView();
        if ($action == 'new') {
            $product->newWithPartOf($value);
        } elseif($action == 'edit') {
            $product->editWithPartOf($value);
        } else {
            $product->indexWithPartOf($value);
        }
    }

    /**
     * ORDER
     * @param $value
     */
    public function order($value)
    {
        $action = $value['action'] ?? filter_input(INPUT_GET, 'action');
        $item = filter_input(INPUT_GET, 'item');

        // NAVBAR ORGANIZATION
        if ($value['@type'] == "Organization") {
            parent::setValues($value);
        } else {
            parent::setValues($value['seller']);
        }

        // NAVBAR
        parent::navbarEdit();

        // MAIN
        $order = new OrderView();
        if ($action == "payment") {
            $order->payment($value);

        } elseif ($action == "expired") {
            $order->expired($value);

        } elseif ($action == 'new') {
            $order->newWithPartOf($value);

        } elseif ($action == 'edit' || $item) {
            $order->editWithPartOf($value);

        } else {
            $order->indexWithPartOf($value);
        }
    }
}
