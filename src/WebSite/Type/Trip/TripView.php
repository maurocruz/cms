<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Trip;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;

class TripView extends TripAbstract
{
    /**
     * @param array $data
     * @return void
     */
    public function index(array $data)
    {
      // NAVBAR
      $this->navbarTrip();

	    // CONTENT
	    WebSite::addMain(Fragment::miscellaneous()->message("Under development!"));

      // TABLE LIST
			WebSite::addMain(Fragment::listTable()
				->labels(_('Name'))
				->rows($data['itemListElement'],['name'])
				->ready()
			);
	    // CONTENT
	    WebSite::addMain('<h4>Under development!</h4>');
    }

    public function new($data = null)
    {
       /* $this->idprovider =$data[0]['identifier']['value'];
        $this->content['main'][] = self::divBox2(sprintf(_("New %s"),'trip'), parent::formTrip());
        // RESPONSE
        return $this->content;*/
    }

    public function edit(array $data)
    {
       /* $value = $data[0];
        $this->idtrip = ArrayTool::searchByValue($value['identifier'],'id','value');
        $this->idprovider = ArrayTool::searchByValue($value['provider']['identifier'],'id','value');
        $this->providerName = $value['provider']['name'];
        // NAVBAR
        $this->navbarTrip($value['name']);
        // TRIP FORM
        $this->content['main'][] = self::divBox2(sprintf(_("Edit %s"),'trip'), parent::formTrip($value));
        // PART OF TRIP
        $this->content['main'][] = self::divBoxExpanding(_("Sub trips"), "Trip", [self::relationshipOneToMany("trip", $this->idtrip, "trip", $value['subTrip'])]);
        // PROPERTY VALUES
        $this->content['main'][] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("trip", $this->idtrip, $value['identifier']) ]);
        // images
        $this->content['main'][] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("trip", $this->idtrip, $value['image']) ]);
        // RESPONSE
        return $this->content;*/
    }
}
