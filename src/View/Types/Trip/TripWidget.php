<?php
namespace Plinct\Cms\View\Types\Trip;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\HtmlPiecesTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Form;
use Plinct\Web\Widget\FormTrait;

class TripWidget {
    protected $content = [];
    protected $idtrip;
    protected $idprovider;
    protected $providerName;

    use HtmlPiecesTrait;
    use navbarTrait;
    use FormElementsTrait;

    protected function navbarTrip($title = null) {
        // ORGANIZATION
        $organizationMenu = [ "/admin/organization/edit/$this->idprovider"=>sprintf(_("View %s"), "organization") ];
        $this->content['navbar'][] = self::navbar(sprintf("%s - %s", $this->providerName, _("travel agency")), $organizationMenu);
        // TRIPS OF ORGANIZATION
        $tripMenu = [
            "/admin/trip?provider=$this->idprovider"=>sprintf(_("View all %s"), _("trips")),
            "/admin/trip/new?provider=$this->idprovider"=>sprintf(_("Add new %s"), _("trip"))
        ];
        $this->content['navbar'][] = self::navbar(sprintf(_("Trips of %s"), $this->providerName), $tripMenu, 3);
        // TRIP
        if($title) {
            $this->content['navbar'][] = self::navbar($title, [], 4);
        }
    }

    protected static function listIndex($tripList): array {
        $columns = [
            "dateModified" => _("Data modified")
        ];
        return self::listAll($tripList,'trip',sprintf(_("List of %s"),_('trips')), $columns);
    }

    protected function formTrip($value = null): array {
        //var_dump($value);
        $case = $value ? 'edit': 'new';
        // FORM
        $form = new Form();
        $form->action("admin/trip/$case")->method('post')->attributes(['class'=>'formPadrao']);
        // HIDDENS
        if ($value) {
            $form->input('id',$this->idtrip,'hidden');
            $form->input('provider',$this->idprovider,'hidden');
        }
        // NAME
        $form->fieldsetWithInput('name',$value['name'],_('Name'));
        // DESCRIPTION
        $form->fieldsetWithTextarea('description',$value['description'],_('Description'));
        // DISAMBIGUATING DESCRIPTION
        $form->fieldsetWithTextarea('disambiguatingDescription',$value['disambiguatingDescription'],_('Disambiguating description'));
        // SUBMIT BUTTONS
        $form->submitButtonSend(['class'=>'button-submit-send']);
        if ($value) $form->submitButtonDelete('/admin/trip/erase',['class'=>'button-submit-delete']);
        // RESPONSE
        return $form->ready();
    }

}