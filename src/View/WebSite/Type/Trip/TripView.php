<?php
namespace Plinct\Cms\Controller\WebSite\Type\Trip;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;

class TripView extends TripAbstract
{
  /**
   * @param array $data
   * @return void
   */
  public function index(array $data): void
  {
		if (isset($data['idorganization'])) {
			$idorganization = $data['idorganization'];
			$organizationName =$data['name'];
			$this->navBarProvider($organizationName, $idorganization);
			parent::listOfProviderTrips($data);

		} else {
			$this->navbarIndex();
			CmsFactory::view()->addMain(_('Show organization with trips'));
			// TABLE
			$table = CmsFactory::view()->fragment()->listTable();
			$table->labels(_('Name'));
			foreach ($data['itemListElement'] as $item) {
				$provider = $item['item']['provider'];
				$id = $provider['idorganization'];
				$table->buttonEdit("/admin/trip?provider=$id");
				$table->addRow($provider['name']);
			}
			CmsFactory::view()->addMain($table->ready());
		}
  }

	/**
	 * @param $data
	 * @return void
	 */
  public function new($data = null): void
  {
		$value = $data ? $data[0] : null;
		parent::navbarIndex();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(parent::formTrip($value),sprintf(_("New %s"),'trip')));
  }

	/**
	 * @throws Exception
	 */
	public function edit(array $data): void
	{
		$value = $data[0];
		$tripId = $value['idtrip'];
		$tripName = $value['name'];
		$provider = $value['provider'];
		$providerName = $provider['name'];
		$providerId = $provider['idorganization'];
		parent::navbarTrip($providerName, $providerId, $tripName);
	  // TRIP FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(parent::formTrip($value),sprintf(_("Edit %s"),'trip')));
		// PART OF the TRIP
    //CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Sub trips"), CmsFactory::view()->fragment()->form()->relationship('trip', $tripId, "trip")->oneToMany($trip['subtrip'] ?? null)));
    // PROPERTY VALUES
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("trip", $tripId, $value['identifier'])));
	  // images
    //CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("trip", $tripId, $trip['image'])));
  }
}
