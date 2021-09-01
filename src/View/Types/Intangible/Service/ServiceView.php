<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\Service;

use Plinct\Cms\Factory\ViewFactory;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Fragment\ListTable\ListTable;
use Plinct\Cms\View\Types\Intangible\Offer\OfferView;
use Plinct\Cms\View\View;
use Plinct\Tool\ArrayTool;

class ServiceView extends ServiceAbstract
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
    public function indexWithPartOf(array $data)
    {
        // VARS
        $this->tableHasPart = lcfirst($data['@type']);
        $this->idHasPart = ArrayTool::searchByValue($data['identifier'],'id','value');
        // NAVBAR
        $this->navbarService();
        // LIST
        $listIndex = Fragment::listTable(['class'=>'table']);
        $listIndex->caption(sprintf(_("List of %s"), _("services")));
        $listIndex->labels(_('Name'), _("Category"), _("Date modified"));
        $listIndex->setEditButton($_SERVER['REQUEST_URI']."&item=");
        $listIndex->rows($data['services']['itemListElement'],['name','category','dateModified']);
        // VIEW
        View::main($listIndex->ready());
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
        // caption
        $listTable->caption(sprintf(_("%s services list"),$value['name']));
        // labels
        $listTable->labels('id',_('Name'),_("Date modified"));
        // rows
        $listTable->rows($value['services']['itemListElement'],['idservice','name','dateModified']);

        // VIEW
        ViewFactory::mainContent($listTable->ready());
    }
}
