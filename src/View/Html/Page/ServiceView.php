<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;

class ServiceView implements ViewInterface
{
    private $content = [];

    private $id;

    use FormElementsTrait;

    private function navbarService($title = null)
    {
        $list2 = [
            "/admin/service" => _("List all"),
            "/admin/service/new" => _("Add new")
        ];
        $search = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "service", "data-searchfor" => "name" ] ];

        $this->content['navbar'][] = navbarTrait::navbar(_("Service"), $list2, 2, $search);

        if ($title) {
            $this->content['navbar'][] = navbarTrait::navbar(_($title),  [
                "/admin/service/edit/".$this->id => _("View it"),
                "/admin/service/order?id=".$this->id => _("List orders"),
                "/admin/order/new?orderedItem=".$this->id."&itemType=service" => _("New order")
            ], 3);
        }
    }

    public function index(array $data): array
    {
        $this->navbarService();
        
        $this->content['main'][] = self::listAll($data, "Service");

        return $this->content;
    }

    public function new($data = null): array
    {
        $this->navbarService();

        $content[] = self::serviceForm();

        $this->content['main'][] = self::divBox(_("Add new"), "service", $content);

        return $this->content;
    }

    public function edit(array $data): array
    {
        if (empty($data)) {
            $this->navbarService();
            $this->content['main'][] = self::noContent();
        } else {
            $value = $data[0];
            $this->id = PropertyValue::extractValue($value['identifier'], "id");

            $this->navbarService($value['name']);

            // form service
            $this->content['main'][] = self::divBox(_("Edit"), "service", [ self::serviceForm("edit", $value) ]);
            // OFFER
            $this->content['main'][] = self::divBox(_("Offer"), "offer", [ (new OfferView())->getForm("service", $this->id, $value['offers']) ]);
            // provider
            $this->content['main'][] = self::divBox(_("Provider"), "organization", [ self::relationshipOneToOne("service", $this->id, "provider", "organization", $value['provider']) ]);
        }

        return $this->content;
    }

    public function order($value)
    {
        $this->id = PropertyValue::extractValue($value['identifier'], "id");
        $this->navbarService($value['name']);

        $this->content['main'][] = self::listAll($value['orders'], "order", sprintf(_("Orders list of %s"), $value['name']));

        return $this->content;
    }

    private function serviceForm($case = "new", $value = null)
    {
        // ID
        $content[] = $case == "edit" ? self::input("id", "hidden", $this->id) : null;
        // NAME
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'], [ "style" => "width: 70%; "]);
        // ADDITIONAL TYPE
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], [ "style" => "width: 30%; "]);
        // DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description']);
        // HAS OFFER CATALOG
        //$content[] = self::fieldsetWithInput(_("Has offer catalog"), "hasOfferCatalog", $value['hasOfferCatalog'], [ "style" => "width: 100%; "]);
        // SERVICE TYPE
        $content[] = self::fieldsetWithInput(_("Service type"), "serviceType", $value['serviceType'], [ "style" => "width: 100%; "]);
        // SERVICE OUTPUT
        //$content[] = self::fieldsetWithInput(_("Service output"), "serviceOutput", $value['serviceOutput'], [ "style" => "width: 100%; "]);
        // TERMS OF SERVICE
        $content[] = self::fieldsetWithTextarea(_("Terms of service"), "termsOfService", $value['termsOfService']);

        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/service/erase") : null;

        return self::form("/admin/service/$case", $content);
    }
}