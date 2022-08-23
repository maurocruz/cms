<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Person;

use Exception;
use Plinct\Cms\Response\View\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\WebSite\Type\Intangible\PostalAddressView;
use Plinct\Cms\WebSite\Type\Intangible\Service\ServiceView;
use Plinct\Cms\WebSite\Type\Product\ProductView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;

class PersonView extends PersonViewAbstract
{
    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navbarPerson();

        $list = Fragment::listTable();
        $list->caption( _("List of persons"));
        $list->labels(_('Name'), _('Date modified'));
        $list->rows($data['itemListElement'],['name','dateModified']);
        $list->setEditButton('/admin/person?id=');
        View::main($list->ready());
    }

    /**
     * @param
     */
    public function new()
    {
        $this->navbarPerson();

        View::main(Fragment::box()->simpleBox(self::formPerson(),_("Add new")));
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function edit(array $data)
    {
        if (!empty($data)) {
            $value = $data[0];

            $this->id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $this->setName($value['name']);

            // NAVBAR
            $this->navbarPersonEdit();

            // FORM
            View::main(Fragment::box()->simpleBox(self::formPerson('edit', $value), _("Edit person")));
            // CONTACT POINT
            View::main(Fragment::box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('person', $this->id, $value['contactPoint'])));
            // ADDRESS
            View::main(Fragment::box()->expandingBox(_("Postal address"), (new PostalAddressView())->getForm("person", $this->id, $value['address'])));
            // IMAGE
            View::main(Fragment::box()->expandingBox(_("Image"), (new ImageObjectView())->getForm("Person", (int) $this->id, $value['image'])));

        } else {
            $this->navbarPerson();
            View::main(Fragment::noContent(_("Person is not exists!")));
        }
    }

    /**
     * @param $value
     */
    public function service($value)
    {
        if ($value['@type'] == 'Service') {
            $this->id = ArrayTool::searchByValue($value['provider']['identifier'], 'id', 'value');
            $this->name = $value['provider']['name'];

            // NAVBAR
            $this->navbarPersonEdit();

            $service = new ServiceView();
            $service->editWithPartOf($value);

        } else {
            $this->id = ArrayTool::searchByValue($value['identifier'], 'id', 'value');
            $this->name = $value['name'];

            // NAVBAR
            $this->navbarPersonEdit();

            $service = new ServiceView();

            if (isset($value['action']) && $value['action'] ==  "new") {
                $service->newWithPartOf($value);
            } else {
                $service->listServices($value);
            }
        }
    }

    public function product($value)
    {
        $action = $value['action'] ?? null;
        $this->name = $value['name'];
        $this->id = ArrayTool::searchByValue($value['identifier'],'id','value');

        // NAVBAR
        $this->navbarPersonEdit();

        // MAIN
        $product = new ProductView();
        if ($action == 'new') {
            $product->newWithPartOf($value);
        } else {
            $product->indexWithPartOf($value);
        }
    }
}
