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

    private function navbarService($title = null, $list = null, $level = 2, $search = null)
    {
        $title = $title ?? _("Service");

        $list = $list ?? [ "/admin/service" => _("List all"), "/admin/service/new" => _("Add new") ];

        $search = $search ?? [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "service", "data-searchfor" => "name" ] ];

        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level, $search);
    }

    public function index(array $data): array
    {
        $this->navbarService();

        $this->content['main'][] = self::listAll($data, "service", null, [ "provider:name" => _("Provider") ]);

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
        $this->navbarService();

        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            $value = $data[0];
            $this->id = PropertyValue::extractValue($value['identifier'], "id");

            $this->navbarService($value['name'], [
                    "/admin/service/edit/".$this->id => _("View it"),
                    "/admin/service/order?id=".$this->id => _("List orders"),
                    "/admin/order/new?orderedItem=".$this->id."&orderedItemType=service" => _("New order")
                ], 3, false
            );

            // form service
            $this->content['main'][] = self::divBox(_("Edit"), "service", [ self::serviceForm("edit", $value) ]);
            // OFFER
            $this->content['main'][] = self::divBox(_("Offer"), "offer", [ (new OfferView())->getForm("service", $this->id, $value['offers']) ]);
        }

        return $this->content;
    }

    public function order($value): array
    {
        $this->id = PropertyValue::extractValue($value['identifier'], "id");
        $this->navbarService($value['name']);

        $this->content['main'][] = self::listAll($value['orders'], "order", sprintf(_("'%s' service order list"), $value['name']), [
            "orderDate" => _("Order date"),
            "customer" => _("Customer"),
            "seller" => _("Seller"),
            "orderStatus" => _("Order status"),
        ]);

        return $this->content;
    }

    public function provider($data): array
    {
        $this->navbarService();

        $providerName = $data['itemListElement'][0]['item']['provider']['name'];

        $this->navbarService(sprintf(_("Services of '%s'"), $providerName), [], 3, false);

        $this->content['main'][] = self::listAll($data, "Service", sprintf(_("Services list of '%s'"), $providerName));

        return $this->content;
    }

    private function serviceForm($case = "new", $value = null): array
    {
        // ID
        $content[] = $case == "edit" ? self::input("id", "hidden", $this->id) : null;
        // PROVIDER
        $content[] = self::fieldset( self::chooseType("provider","organization,person", $value['provider'], "name", [ "style" => "display: flex;"]), _("Provider"), [ "style" => "width: 100%" ]);
        // NAME
        $content[] = self::fieldsetWithInput(_("Service name"), "name", $value['name'], [ "style" => "width: 70%; "]);
        // ADDITIONAL TYPE
        $content[] = self::fieldsetWithInput(_("Additional type"), "additionalType", $value['additionalType'], [ "style" => "width: 30%; "]);
        // DESCRIPTION
        $content[] = self::fieldsetWithTextarea(_("Description"), "description", $value['description']);
        // HAS OFFER CATALOG
        //$content[] = self::fieldsetWithInput(_("Has offer catalog"), "hasOfferCatalog", $value['hasOfferCatalog'], [ "style" => "width: 100%; "]);
        // SERVICE TYPE
        $content[] = self::fieldsetWithInput(_("Service type"), "serviceType", $value['serviceType'], [ "style" => "width: 100%; "]);
        // TERMS OF SERVICE
        $content[] = self::fieldsetWithTextarea(_("Terms of service"), "termsOfService", $value['termsOfService']);

        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/service/erase") : null;

        return self::form("/admin/service/$case", $content);
    }
}