<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Offer;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Tool\ToolBox;

abstract class OfferAbstract
{
	protected ?string $iditemOffered = null;
	/**
	 *
	 */
	protected function navbarOffer()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_("Offer"))
				->type('offer')
				->level(4)
				->newTab('/admin/offer'.($this->iditemOffered ? "?offeredBy=$this->iditemOffered" : ''), CmsFactory::view()->fragment()->icon()->home(16,16))
				->newTab('/admin/offer/new'.($this->iditemOffered ? "?offeredBy=$this->iditemOffered" : ''), CmsFactory::view()->fragment()->icon()->plus(16,16))
				->ready()
		);
	}

	protected function navbarOfferedBy(array $offeredBy)
	{
		$tbOfferedBy = ToolBox::typeBuilder($offeredBy);
		$idofferedBy = $tbOfferedBy->getPropertyValue('idthing');
		$this->iditemOffered = $idofferedBy;
		if ($tbOfferedBy->getType() == 'Organization') {
			Organization::navbarIndex();
			Organization::navbarEdit($tbOfferedBy->getValue('name'), $tbOfferedBy->getId(), $idofferedBy);
		}
		$this->navbarOffer();
	}
	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	protected function formOffer(string $case = 'new', array $value = null): array
	{
		if ($value) {
			$tbOffer = ToolBox::typeBuilder($value);
			$idoffer = $tbOffer->getId();
		}
		$form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-offer']);
		$form->action("/admin/offer/$case")->method('post');
		if ($case == 'edit') { $form->input("idoffer", $idoffer, "hidden"); }
		if ($this->iditemOffered) {
			$form->input('offeredBy', $this->iditemOffered, "hidden");
		}
		// THING
		$form = Thing::formContent($form, $value);
		// PRICE
		$form->fieldsetWithInput("price", $value['price'] ?? null, _("Price"), "number", null, [ "min" => 0, "step" => "any" ]);
		// PRICE CURRENCY
		$form->fieldsetWithInput("priceCurrency", $value['priceCurrency'] ?? "R$",_("Price currency"), "text", null, [ "maxlength" => "2" ]);
		// AVAILABILITY
		$form->fieldsetWithSelect("availability", $value['availability'] ?? null, [
			"Discontinued" => _("Discontinued"),
			"InStock" => _("In stock"),
			"InStoreOnly" => _("In store only"),
			"LimitedAvailability" => _("Limited availability"),
			"OnlineOnly" => _("Online only"),
			"OutOfStock" => _("Out of stock"),
			"PreOrder" => _("Pre order"),
			"PreSale" => _("Pre sale"),
			"SoldOut" => _("Sould out")
		], _("Availability"));
		// ELEGIBLE QUANTITY
		$form->fieldsetWithInput("elegibleQuantity", $value['elegibleQuantity'] ?? null, _("Elegible quantity"));
		// ELEGIBLE DURATION
		$form->fieldsetWithInput("elegibleDuration", $value['elegibleDuration'] ?? null, _("Elegible duration"));
		// VALID THROUGH
		$form->fieldsetWithInput("validThrough", $value['validThrough'] ?? null, _("Valid through"),"dateTime-local");
		// SUBMIT
		$form->submitButtonSend();
		if ($value) $form->submitButtonDelete("/admin/offer/erase");
		// READY
		return $form->ready();
	}
}
