<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Person;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\ContactPointView;
use Plinct\Cms\View\Types\Intangible\PostalAddressView;
use Plinct\Cms\View\Types\Intangible\Service\ServiceView;
use Plinct\Cms\View\Types\Product\ProductView;
use Plinct\Cms\View\Types\TypeViewInterface;
use Plinct\Cms\View\View;
use Plinct\Tool\ArrayTool;

class PersonView extends PersonViewAbstract implements TypeViewInterface
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
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navbarPerson();
        MainView::content([ "tag" => "h3", "content" => _("Add new person") ]);
        MainView::content(self::formPerson());
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function edit(array $data)
    {
        if (!empty($data)) {
            $value = $data[0];

            $this->id = (int)ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $this->setName($value['name']);

            // NAVBAR
            $this->navbarPersonEdit();

            // FORM
            MainView::content(self::divBox2(_("Edit person"), [ self::formPerson('edit', $value) ]));
            // CONTACT POINT
            MainView::content(self::divBoxExpanding(_("Contact point"), "ContactPoint", [(new ContactPointView())->getForm('person', $this->id, $value['contactPoint'])]));
            // ADDRESS
            MainView::content(self::divBoxExpanding(_("Postal address"), "PostalAddress", [(new PostalAddressView())->getForm("person", $this->id, $value['address'])]));
            // IMAGE
            MainView::content(self::divBoxExpanding(_("Image"), "ImageObject", [(new ImageObjectView())->getForm("Person", $this->id, $value['image'])]));

        } else {
            $this->navbarPerson();
            MainView::content(self::noContent(_("Person is not exists!")));
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
