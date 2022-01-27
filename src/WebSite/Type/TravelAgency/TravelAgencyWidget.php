<?php
namespace Plinct\Cms\WebSite\Type\TravelAgency;

use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Widget\navbarTrait;

class TravelAgencyWidget {
    protected $content = [];
    protected $idorganization;

    use HtmlPiecesTrait;
    use navbarTrait;

    protected function navBarTravelAgency($title = null) {
        $this->content['navbar'][] = self::navbar(_("Travel agencies"));
        if ($title) {
            $list = [
                "/admin/organization/edit/$this->idorganization"=>sprintf(_("View %s"), 'organization'),
                "/admin/trip?provider=$this->idorganization"=>sprintf(_("View all %s"), _("trips")),
                "/admin/trip/add?provider=$this->idorganization"=>sprintf(_("Add new %s"), _("trip"))
            ];
            $this->content['navbar'][] = self::navbar(sprintf("%s - %s", $title, _("Travel agency")), $list, 3);
        }
    }
}