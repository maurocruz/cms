<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Exception;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Cms\WebSite\WebSite;

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

			View::main(_('Show organization with trips'));
			// TABLE
			$table = Fragment::listTable();
			$table->labels(_('Name'));
			foreach ($data['itemListElement'] as $item) {
				$provider = $item['item']['provider'];
				$id = $provider['idorganization'];
				$table->buttonEdit("/admin/trip?provider=$id");
				$table->addRow($provider['name']);
			}
			WebSite::addMain($table->ready());
		}
  }

  public function new($data = null)
  {
		$value = $data ? $data[0] : null;
		parent::navbarIndex();
		View::main(Fragment::box()->simpleBox(parent::formTrip($value),sprintf(_("New %s"),'trip')));
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
    View::main(Fragment::box()->simpleBox(parent::formTrip($trip),sprintf(_("Edit %s"),'trip')));
		// PART OF TRIP
    View::main(Fragment::box()->expandingBox(_("Sub trips"), Fragment::form()->relationship('trip', $tripId, "trip")->oneToMany($trip['subtrip'] ?? null)));
    // PROPERTY VALUES
    View::main(Fragment::box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("trip", $tripId, $trip['identifier'])));
	  // images
    View::main(Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("trip", (int) $tripId, $trip['image'])));
  }
}
