<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\PropertyValueView;

class TripView extends TripAbstract
{
  /**
   * @param array $data
   * @return void
   */
  public function index(array $data)
  {
		if (isset($data['idorganization'])) {
			$idorganization = $data['idorganization'];
			$organizationName =$data['name'];
			$this->navBarProvider($organizationName, $idorganization);
			parent::listOfProviderTrips($data);

		} else {
			$this->navbarIndex();

			CmsFactory::webSite()->addMain(_('Show organization with trips'));
			// TABLE
			$table = CmsFactory::response()->fragment()->listTable();
			$table->labels(_('Name'));
			foreach ($data['itemListElement'] as $item) {
				$provider = $item['item']['provider'];
				$id = $provider['idorganization'];
				$table->buttonEdit("/admin/trip?provider=$id");
				$table->addRow($provider['name']);
			}
			CmsFactory::webSite()->addMain($table->ready());
		}
  }

  public function new($data = null)
  {
		$value = $data ? $data[0] : null;
		parent::navbarIndex();
		CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(parent::formTrip($value),sprintf(_("New %s"),'trip')));
  }

	/**
	 * @throws Exception
	 */
	public function edit(array $data)
  {
		$trip = $data[0];
		$tripId = $trip['idtrip'];
		$tripName = $trip['name'];
		$provider = $trip['provider'];
		$providerName = $provider['name'];
		$providerId = $provider['idorganization'];

		parent::navbarTrip($providerName, $providerId, $tripName);

	  // TRIP FORM
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(parent::formTrip($trip),sprintf(_("Edit %s"),'trip')));
		// PART OF TRIP
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Sub trips"), CmsFactory::response()->fragment()->form()->relationship('trip', $tripId, "trip")->oneToMany($trip['subtrip'] ?? null)));
    // PROPERTY VALUES
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("trip", $tripId, $trip['identifier'])));
	  // images
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("trip", $tripId, $trip['image'])));
  }
}
