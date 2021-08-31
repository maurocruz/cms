<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Organization;

use Exception;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\Types\Intangible\ContactPointView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\Order\OrderView;
use Plinct\Cms\View\Types\Product\ProductView;
use Plinct\Cms\View\Types\TypeViewInterface;
use Plinct\Cms\View\View;

class OrganizationView extends OrganizationAbstract implements TypeViewInterface
{
    /**
     * @param array $data
     */
    public function index(array $data)
    {
        // NAVBAR
        parent::navbarIndex();
        //
        if (isset($data['errorInfo'])) {
            View::main(self::errorInfo($data['errorInfo'], "organization"));
        } else {
            View::main(self::listAll($data, "organization", _("List of organizations"), [ "additionalType" => "Additional type", "dateModified" => "Date Modified" ]));
        }
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        // NAVBAR
        parent::navbarNew();
        //
        View::main(self::divBox(_("Add organization"), "Organization", [ self::formOrganization() ]));
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
            MainView::content(parent::noContent(_("No item founded!")));
        } else {
            $value = parent::setValues($data[0]);
            // NAVBAR
            parent::navbarEdit();
            // organization
            View::main(self::divBox2(sprintf(_("Edit %s"), _("organization")), [self::formOrganization('edit', $value)]));
            // location
            View::main(self::divBoxExpanding(_("Place"), "Place", [self::relationshipOneToOne("organization", $this->id, "location", "place", $value['location'])]));
            // contact point
            View::main(self::divBoxExpanding(_("Contact point"), "ContactPoint", [(new ContactPointView())->getForm('organization', $this->id, $value['contactPoint'])]));
            // member
            View::main(self::divBoxExpanding(_("Persons"), "Person", [self::relationshipOneToMany("organization", $this->id, "person", $value['member'])]));
            // image
            View::main(self::divBoxExpanding(_("Images"), "ImageObject", [(new ImageObjectView())->getForm("organization", $this->id, $value['image'])]));
        }
    }

    /**
     * @throws Exception
     */
    public function service($data): array
    {
        parent::setValues($data[0]);
        // NAVBAR
        parent::navbarEdit();

        return $this->itemView("Service", $data);
    }

    /**
     * @param $value
     * @throws Exception
     */
    public function product($value)
    {
        $action = $value['action'] ?? null;

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
     * @param $data
     * @throws Exception
     */
    public function order($data)
    {
        // VARS
        $action = filter_input(INPUT_GET, 'action');

        // NAVBAR ORGANIZATION
        if ($data[0]['@type'] == "Organization") {
            parent::setValues($data[0]);
        } else {
            parent::setValues($data[0]['seller']);
        }

        // NAVBAR
        parent::navbarEdit();

        // MAIN
        $order = new OrderView();
        if ($action == "payment") {
            $order->payment($data[0]);
        }
        elseif ($action == "expired") {
            $order->expired($data[0]);
        } else {
            $response = $this->itemView("Order", $data);
            View::main($response['main']);
        }
    }
}
