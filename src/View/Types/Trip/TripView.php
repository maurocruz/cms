<?php

namespace Plinct\Cms\View\Types\Trip;

use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\PropertyValueView;
use Plinct\Cms\View\ViewInterface;
use Plinct\Tool\ArrayTool;

class TripView extends TripWidget implements ViewInterface {

    public function index(array $data): array {
        $this->idprovider = ArrayTool::searchByValue($data['identifier'],'id','value');
        $this->providerName = $data['name'];
        $tripList = $data['trip'];
        // NAVBAR
        $this->navbarTrip();
        // TABLE LIST
        $this->content['main'][] = parent::listIndex($tripList);
        // RESPONSE
        return $this->content;
    }

    public function new($data = null): array {
        $this->idprovider =$data[0]['identifier']['value'];
        $this->content['main'][] = self::divBox2(sprintf(_("New %s"),'trip'), parent::formTrip());
        // RESPONSE
        return $this->content;
    }

    public function edit(array $data): array {
        $value = $data[0];
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
        return $this->content;
    }
}