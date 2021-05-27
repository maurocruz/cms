<?php
namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\View\ViewInterface;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Widget\navbarTrait;

class ServiceView extends ServiceWidget implements ViewInterface {

    private function navbarService($title = null, $list = null, $level = 2, $search = null) {
        $title = $title ?? _("Service");
        $list = $list ?? [ "/admin/service" => _("List all"), "/admin/service/new" => _("Add new") ];
        $search = $search ?? [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "service", "data-searchfor" => "name" ] ];
        $this->content['navbar'][] = navbarTrait::navbar($title, $list, $level, $search);
    }

    public function index(array $data): array {
        $this->content['main'][] = parent::noContent("Method not done!");
        return $this->content;
    }

    public function new($data = null): array {
        return $this->content;
    }
    public function edit(array $data): array {
        $this->content['main'][] = parent::noContent("Method not done!");
        return $this->content;
    }

    public function indexWithPartOf(array $data): array {
        $columnRows = [
            "idservice" => [ "ID", [ "style" => "width: 30px;" ]],
            "name" => _("Name"),
            "category" => [ _("Category"), [ "style" => "width: 200px;" ]],
            "serviceType" => [ _("Service type"), [ "style" => "width: 300px;"] ],
            "dateModified" => [ _("Date modified"), [ "style" => "width: 150px;"] ]
        ];
        $this->content['main'][] = HtmlPiecesTrait::indexWithSubclass($data, "service", $columnRows, $data['services']['itemListElement'] );
        return $this->content;
    }

    public function newWithPartOf($data = null): array {
        $this->content['main'][] = self::divBox2(sprintf(_("Add new %s from %s"), _("service"), $data['provider']['name']), [ parent::serviceForm("new", $data) ]);
        return $this->content;
    }

    public function editWithPartOf(array $data): array {
        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            // EDIT SERVICE
            $this->content['main'][] = self::divBox(_("Edit"), "service", [ self::serviceForm("edit", $data) ]);
            // OFFER
            $this->content['main'][] = self::divBoxExpanding(_("Offer"), "offer", [ (new OfferView())->edit($data) ]);
        }
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
