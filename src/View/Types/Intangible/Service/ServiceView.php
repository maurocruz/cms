<?php
namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\View\ViewInterface;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class ServiceView extends ServiceWidget implements ViewInterface {

    private function navbarService() {
        $title =  _("Service");
        $list = [
            "/admin/$this->tableHasPart/service?id=$this->idHasPart" => _("List all"),
            "/admin/$this->tableHasPart/service?id=$this->idHasPart&action=new" => _("Add new") ]
        ;
        $this->content['navbar'][] = navbarTrait::navbar($title,$list,4);
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
        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['identifier'],'id','value');
        $this->navbarService();
        $columnRows = [
            "idservice" => [ "ID", [ "style" => "width: 30px;" ]],
            "name" => _("Name"),
            "serviceType" => [ _("Service type"), [ "style" => "width: 300px;"] ],
            "category" => [ _("Category"), [ "style" => "width: 200px;" ]],
            "dateModified" => [ _("Date modified"), [ "style" => "width: 150px;"] ]
        ];
        $this->content['main'][] = HtmlPiecesTrait::indexWithSubclass($data, "service", $columnRows, $data['services']['itemListElement'] );
        return $this->content;
    }

    public function newWithPartOf($data = null): array {
        $this->tableHasPart = lcfirst($data['provider']['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['provider']['identifier'],'id','value');
        $this->content['main'][] = self::divBox2(sprintf(_("Add new %s from %s"), _("service"), $data['provider']['name']), [ parent::serviceForm("new", $data) ]);
        return $this->content;
    }

    public function editWithPartOf(array $data): array {
        $this->tableHasPart = lcfirst($data['provider']['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['provider']['identifier'],'id','value');
        if (empty($data)) {
            $this->content['main'][] = self::noContent();
        } else {
            // EDIT SERVICE
            $this->content['main'][] = self::divBox(sprintf("%s %s",_("Edit"),_("service")), "service", [ self::serviceForm("edit", $data) ]);
            // OFFER
            $this->content['main'][] = self::divBoxExpanding(_("Offer"), "offer", [ (new OfferView())->edit($data) ]);
        }
        return $this->content;
    }

    /*public function provider($data): array {
        $this->navbarService();
        $providerName = $data['itemListElement'][0]['item']['provider']['name'];
        $this->navbarService(sprintf(_("Services of '%s'"), $providerName), [], 3, false);
        $this->content['main'][] = self::listAll($data, "Service", sprintf(_("Services list of '%s'"), $providerName));
        return $this->content;
    }*/
}
