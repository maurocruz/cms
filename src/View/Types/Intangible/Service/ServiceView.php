<?php
namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\View\Html\Page\ViewInterface;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class ServiceView extends ServiceWidget implements ViewInterface {

    private function navbarService($title = null, $list = null, $level = 2, $search = null) {
        $title = $title ?? _("Service");
        $list = $list ?? [ "/admin/service" => _("List all"), "/admin/service/new" => _("Add new") ];
        $search = $search ?? [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "service", "data-searchfor" => "name" ] ];
        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level, $search);
    }

    public function index(array $data): array {
        $columnRows = [
            "name" => _("Name"),
            "additionalType" => _("AddtionalType"),
            "category" => _("Category"),
            "dateCreated" => _("Date created"),
            "dateModified" => _("Date modified")
        ];
        $this->content['main'][] = HtmlPiecesTrait::indexWithSubclass($data['name'], "services", $columnRows, $data['services']['itemListElement'] );
        return $this->content;
    }

    public function new($data = null): array {
        $this->content['main'][] = self::divBox2(sprintf(_("Add new %s from %s"), _("service"), $data['provider']['name']), [ parent::serviceForm("new", $data) ]);
        return $this->content;
    }

    public function edit(array $data): array {
        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            // EDIT SERVICE
            $this->content['main'][] = self::divBox(_("Edit"), "service", [ self::serviceForm("edit", $data) ]);
            // OFFER
            $this->content['main'][] = self::divBox(_("Offer"), "offer", [ (new OfferView())->edit($data) ]);
        }
        return $this->content;
    }

    public function order($value): array {
        $this->id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $this->navbarService($value['name']);
        $this->content['main'][] = self::listAll($value['orders'], "order", sprintf(_("'%s' service order list"), $value['name']), [
            "orderDate" => _("Order date"),
            "customer" => _("Customer"),
            "seller" => _("Seller"),
            "orderStatus" => _("Order status"),
        ]);
        return $this->content;
    }

    public function provider($data): array {
        $this->navbarService();
        $providerName = $data['itemListElement'][0]['item']['provider']['name'];
        $this->navbarService(sprintf(_("Services of '%s'"), $providerName), [], 3, false);
        $this->content['main'][] = self::listAll($data, "Service", sprintf(_("Services list of '%s'"), $providerName));
        return $this->content;
    }
}
