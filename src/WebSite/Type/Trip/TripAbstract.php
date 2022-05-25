<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;

class TripAbstract
{
	protected string $idprovider;
	protected string $idtrip;

	protected function navbarTrip($title = null)
  {
		WebSite::addHeader(Fragment::navbar()
			->title('Trips')
			->ready()
		);
    // ORGANIZATION
    /*$organizationMenu = [ "/admin/organization/edit/$this->idprovider"=>sprintf(_("View %s"), "organization") ];
    $this->content['navbar'][] = self::navbar(sprintf("%s - %s", $this->providerName, _("travel agency")), $organizationMenu);
    // TRIPS OF ORGANIZATION
    $tripMenu = [
        "/admin/trip?provider=$this->idprovider"=>sprintf(_("View all %s"), _("trips")),
        "/admin/trip/new?provider=$this->idprovider"=>sprintf(_("Add new %s"), _("trip"))
    ];
    $search = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "trip", "data-searchfor" => "name" ] ];
    $this->content['navbar'][] = self::navbar(sprintf(_("Trips of %s"), $this->providerName), $tripMenu, 3, $search);*/
    // TRIP
    if($title) {
        WebSite::addHeader(Fragment::navbar()
	        ->title($title)
	        ->ready()
        );
    }
  }

  protected function formTrip($value = null): array
  {
    $name = $value['name'] ?? null;
    $description = $value['description'] ?? null;
    $disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
    $arrivalDate = $value['arrivalDate'] ?? null;
    $arrivalTime = $value['arrivalTime'] ?? null;
    $departureDate = $value['departureDate'] ?? null;
    $departureTime = $value['departureTime'] ?? null;
    $case = $value ? 'edit': 'new';
    // FORM
    $form = Fragment::form();
    $form->action("/admin/trip/$case")->method('post')->attributes(['class'=>'formPadrao form-trip']);
    $form->input('provider', $this->idprovider, 'hidden');
    // HIDDENS
    if ($value) {
        $form->input('id', $this->idtrip, 'hidden');
    }
    // NAME
    $form->fieldsetWithInput('name', $name, _('Name'));
    // DESCRIPTION
    $form->fieldsetWithTextarea('description', $description, _('Description'));
    // DISAMBIGUATING DESCRIPTION
    $form->fieldsetWithTextarea('disambiguatingDescription', $disambiguatingDescription, _('Disambiguating description'));
    // ARRIVAL DATE
    $form->fieldsetWithInput('arrivalDate',$arrivalDate, _("Arrival date"), 'date');
    // ARRIVAL TIME
    $form->fieldsetWithInput('arrivalTime', $arrivalTime, _("Arrival time"), 'time');
    // DEPARTURE DATE
    $form->fieldsetWithInput('arrivalDate', $departureDate, _("Departure date"), 'date');
    // DEPARTURE TIME
    $form->fieldsetWithInput('departureTime', $departureTime, _("Departure time"), 'time');
    // SUBMIT BUTTONS
    $form->submitButtonSend(['class'=>'button-submit-send']);
    if ($value) $form->submitButtonDelete('/admin/trip/erase',['class'=>'button-submit-delete']);
    // RESPONSE
    return $form->ready();
  }
}
