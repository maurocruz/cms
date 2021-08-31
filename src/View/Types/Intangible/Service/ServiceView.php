<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\Factory\ViewFactory;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Fragment\ListTable\ListTable;
use Plinct\Cms\View\Types\TypeViewInterface;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Tool\ArrayTool;

class ServiceView extends ServiceAbstract implements TypeViewInterface
{
    /**
     *
     */
    private function navbarService()
    {
        View::navbar(_("Services"), [
            "/admin/$this->tableHasPart?id=$this->idHasPart&action=service" => Fragment::icon()->home(),
            "/admin/$this->tableHasPart/service?id=$this->idHasPart&action=new" => Fragment::icon()->plus()
        ], 4);
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        ViewFactory::mainContent(Fragment::miscellaneous()->message("Method not done!"));
    }

    /**
     * @param null $data
     * @return void
     */
    public function new($data = null)
    {
        ViewFactory::mainContent(Fragment::miscellaneous()->message("Method not done!"));
    }

    /**
     * @param array $data
     */
    public function edit(array $data)
    {
        ViewFactory::mainContent(Fragment::miscellaneous()->message("Method not done!"));
    }

    /**
     * @param array $data
     */
    public function indexWithPartOf(array $data)
    {
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
        ViewFactory::mainContent(HtmlPiecesTrait::indexWithSubclass($data, "service", $columnRows, $data['services']['itemListElement'] ));
    }

    /**
     * @param null $data
     */
    public function newWithPartOf($data = null)
    {
        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['identifier'],'id','value');
        // NAVBAR
        $this->navbarService();
        // FORM
        parent::newWithPartOfForm();
    }

    /**
     * @param array $data
     */
    public function editWithPartOf(array $data)
    {
        $this->tableHasPart = lcfirst($data['provider']['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['provider']['identifier'],'id','value');

        // NAVBAR
        $this->navbarService();

        if (empty($data)) {
            View::main(Fragment::miscellaneous()->message());
        } else {
            // EDIT SERVICE
            View::main(self::serviceForm("edit", $data));
            // OFFER
            View::main(Fragment::box()->expandingBox( _("Offer"), (new OfferView())->editWithPartOf($data) ));
        }
    }

    /**
     * @param $value
     */
    public function listServices($value)
    {
        $this->tableHasPart = lcfirst($value['@type']);
        $this->idHasPart = ArrayTool::searchByValue($value['identifier'],'id','value');

        // NAVBAR
        $this->navbarService();

        // LIST TABLE IN MAIN
        $listTable = new ListTable();
        $listTable->setEditButton("/admin/$this->tableHasPart/service?id=$this->idHasPart&item=");
        // CAPTION
        $listTable->caption(sprintf(_("%s services list"),$value['name']));
        // LABELS
        $listTable->labels('id',_('Name'),_("Date modified"));
        // ROWS
        $listTable->rows($value['services']['itemListElement'],['idservice','name','dateModified']);
        // READY
        ViewFactory::mainContent($listTable->ready());
    }
}
