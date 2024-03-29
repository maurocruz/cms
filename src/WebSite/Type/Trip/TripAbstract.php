<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Cms\WebSite\WebSite;

class TripAbstract
{
	/**
	 * @return void
	 */
	protected function navbarIndex()
	{
		WebSite::addHeader(Fragment::navbar()
			->type('trip')
			->title('Trips')
			->newTab('/admin/trip', Fragment::icon()->home())
			->newTab('/admin/trip/new', Fragment::icon()->plus())
			->search('/admin/trip')
			->level(2)
			->ready()
		);
	}

	/**
	 * @param $name
	 * @param $id
	 * @return void
	 */
	protected function navBarProvider($name, $id)
	{
		self::navbarIndex();
		View::contentHeader(Fragment::navbar()
			->title($name)
			->newTab("/admin/trip?provider=$id", Fragment::icon()->home())
			->newTab("/admin/trip?provider=$id&action=new", Fragment::icon()->plus())
			->level(3)
			->ready()
		);
	}

	/**
	 * @param $providerName
	 * @param $providerId
	 * @param $tripName
	 * @return void
	 */
	protected function navbarTrip($providerName, $providerId, $tripName)
  {
		self::navBarProvider($providerName, $providerId);
    WebSite::addHeader(Fragment::navbar()
      ->title($tripName)
	    ->level(4)
      ->ready()
    );
  }

	/**
	 * @param $data
	 * @return void
	 */
	protected function listOfProviderTrips($data)
	{
		$table = Fragment::listTable();
		$table->labels(_('Name'),_('Date modified'));

		foreach ($data['trips']['itemListElement'] as $item) {
			$trip = $item['item'];
			$id = $trip['idtrip'];
			$table->buttonEdit("/admin/trip/edit/$id");
			$table->addRow($trip['name'], $trip['dateModified']);
		}
		View::main($table->ready());
	}

	/**
	 * @param $value
	 * @return array
	 */
  protected function formTrip($value = null): array
  {
		$idtrip = $value['idtrip'] ?? null;
    $name = $value['name'] ?? null;
    $description = $value['description'] ?? null;
    $disambiguatingDescription = $value['disambiguatingDescription'] ?? null;
    $arrivalDate = $value['arrivalDate'] ?? null;
    $arrivalTime = $value['arrivalTime'] ?? null;
    $departureDate = $value['departureDate'] ?? null;
    $departureTime = $value['departureTime'] ?? null;
    $case = $value ? 'edit': 'new';
		$provider= $value['provider'] ?? null;

    // FORM
    $form = Fragment::form();
    $form->action("/admin/trip/$case")->method('post')->attributes(['class'=>'formPadrao form-trip']);
    // HIDDENS
    if ($idtrip) $form->input('id', $idtrip, 'hidden');
    // NAME
    $form->fieldsetWithInput('name', $name, _('Name'));
	  // PROVIDER
	  $form->fieldset($form->chooseType('provider','organization',$provider), _('Provider'));
    // DESCRIPTION
    $form->fieldsetWithTextarea('description', $description, _('Description'));
    // DISAMBIGUATING DESCRIPTION
    $form->fieldsetWithTextarea('disambiguatingDescription', $disambiguatingDescription, _('Disambiguating description'), ['class'=>'form-trip-disambiguatingDescription']);
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
