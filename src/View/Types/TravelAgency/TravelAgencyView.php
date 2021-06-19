<?php
namespace Plinct\Cms\View\Types\TravelAgency;

use Plinct\Cms\View\ViewInterface;
use Plinct\Tool\ArrayTool;

class TravelAgencyView extends TravelAgencyWidget implements ViewInterface {

    public function index(array $data): array {
        // NAVBAR
        $this->navBarTravelAgency();
        // TABLE
        $this->content['main'][] = self::listAll($data,'travelAgency', sprintf(_("List of %s"), _("travel agencies")));
        // RESPONSE
        return $this->content;
    }

    public function new($data = null): array {
        return $this->content;
    }

    public function edit(array $data): array {
        $name =$data['name'];
        $this->idorganization = ArrayTool::searchByValue($data['identifier'],'id','value');
        // NAVBAR
        $this->navBarTravelAgency($name);
        return $this->content;
    }
}