<?php
namespace Plinct\Cms\WebSite\Type\TravelAgency;

use Plinct\Tool\ArrayTool;

class TravelAgencyView extends TravelAgencyWidget
{

    public function index(array $data): array {
        // NAVBAR
        $this->navBarTravelAgency();
        // TABLE
        $this->content['main'][] = self::listAll($data,'travelAgency', sprintf(_("List of %s"), _("travel agencies")));
        // RESPONSE
        return $this->content;
    }

    public function new(): array {
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