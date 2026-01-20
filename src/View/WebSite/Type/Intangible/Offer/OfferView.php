<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Offer;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\ThingView;
use Plinct\Tool\ToolBox;

class OfferView extends ThingView
{
	private ?string $idoffer = null;
	private ?string $offeredBy = null;
	private ?string $itemOffered = null;


	public function __construct(string $type = 'thing', string $sitemapFilename = "sitemap-offer.xml")
	{
		parent::__construct($type, $sitemapFilename);
	}

	public function __destruct() {
		parent::__destruct();

		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_("Offer"))
				->type('offer')
				->level(4)
				->newTab("/admin/offer?itemOffered=$this->itemOffered", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/offer/new?itemOffered=$this->itemOffered", CmsFactory::view()->fragment()->icon()->plus())
				->ready()
		);

		if ($this->idoffer) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->title($this->name)
					->level(5)
					->ready()
			);
		}
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 */
  public function index(?array $data, array $queryParams = null): void
  {
		$itemOffered = $queryParams['itemOffered'] ?? '';
		$offeredBy = $queryParams['offeredBy'] ?? '';
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('offer')->setDataset('itemOffered', $itemOffered)->setDataset('offeredBy',$offeredBy)->ready()
		);
  }

  /**
   * @param null $data
   * @param array|null $queryParams
   */
  public function new($data = null, array $queryParams = null): void
  {
	  CmsFactory::view()->addMain(
	    CmsFactory::view()->fragment()->box()->simpleBox(self::formOffer(),_('New offer'))
	  );
  }

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null): void
	{
		$value = $data[0];
		$tbOffer = ToolBox::typeBuilder($value);
		$this->name = $value['name'];
		$this->idthing = $tbOffer->getIdthing();
		$this->idoffer = $tbOffer->getId();
		$this->offeredBy = $value['offeredBy'];
		// item offered
		$itemOffered = $value['itemOffered'];
		$tbItemOffered = ToolBox::typeBuilder($itemOffered);
		$itemOfferedName = $itemOffered['name'];
		$itemOfferedType = lcfirst($tbItemOffered->getType());
		$itemOfferedId = $tbItemOffered->getId();
		$this->itemOffered = $tbItemOffered->getIdthing();

		CmsFactory::view()->addMain([
			CmsFactory::view()->fragment()->box()->simpleBox(self::formOffer('edit',$value),_('Offer'), $this->idthing, $this->name),
			CmsFactory::view()->fragment()->box()->simpleBox("<p><a href='/admin/$itemOfferedType/edit/$itemOfferedId'>$itemOfferedName</a></p>",_('Item offered'))
			]
		);
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	protected function formOffer(string $case = 'new', array $value = null): array
	{
		$form = CmsFactory::view()->fragment()->form("form-offer", ['class'=>'form-basic form-offer']);
		$form->action("/admin/offer/$case")->method('post');
		$form->addMandatories('itemOffered','price','eligibleQuantity','availability','validThrough');
		$form->input('offeredBy', $this->offeredBy, "hidden");
		$currencies = CmsFactory::toolBox()::currencies();
		if ($this->itemOffered ) {
			$form->input('itemOffered', $this->itemOffered, "hidden");
		}
		if ($case == 'edit') {
			$tbOffer = ToolBox::typeBuilder($value);
			$idoffer = $tbOffer->getId();
			$form->input("idoffer", $idoffer, "hidden");
			$form->setIdform('form-offer-edit'.$idoffer);
		} else {
			$form->setIdform('form-offer-new');
		}
		// THING
		$form = parent::formThingContent($form, $value);
		// OFFERED BY
		$form->relationshipOneToOne('Organization,Person',_('Offered by'),'offeredBy',$this->offeredBy);
		// ITEM OFFERED
		$form->relationshipOneToOne('service,product',_('Item offered'),'itemOffered',$this->itemOffered);
		// PRICE CURRENCY
		$form->fieldsetWithSelect('priceCurrency', $value['priceCurrency'] ?? null, $currencies, _('Currency'));
		// PRICE
		$form->fieldsetWithInput("price", $value['price'] ?? null, _("Price"), "number", null, [ "min" => 0, "step" => "any" ]);
		// ELEGIBLE QUANTITY
		$form->fieldsetWithInput("eligibleQuantity", $value['eligibleQuantity'] ?? null, _("Elegible quantity"));
		// ELEGIBLE DURATION
		$form->fieldsetWithInput("eligibleDuration", $value['eligibleDuration'] ?? null, _("Elegible duration"));
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
		// VALID THROUGH
		$form->fieldsetWithInput("validThrough", $value['validThrough'] ?? null, _("Valid through"),"dateTime-local");
		// SUBMIT
		$form->submitButtonSend();
		if ($value) $form->submitButtonDelete("/admin/offer/erase");
		// READY
		return $form->ready();
	}

	/**
	 * @param array $offerList
	 * @return string
	 */
	public static function listOffers(array $offerList): string
	{
		$table = '<table>
				<thead>
					<tr>
						<th>#</th>
						<th>'._('Action').'</th>
						<th>idoffer</th>
						<th>'._('Name').'</th>
						<th>'._('Price').'</th>
						<th>'._('Availability').'</th>
						<th>'._('Valid through').'</th>
						<th>'._('Date created').'</th>
					</tr>
				</thead>
			<tbody>';
		foreach ($offerList as $key => $offer) {
			$tbOffer = ToolBox::typeBuilder($offer);
			$name = $offer['name'];
			$idoffer = $tbOffer->getId();
			$dateCreated = $tbOffer->getPropertyValue('dateCreated');
			$price = ToolBox::NumberFormatterCurrency()->format($offer['price']);
			$availability = $offer['availability'];
			$validThrough = $offer['validThrough'];
			$table .= "<tr>
					<td>".($key+1)."</td>
					<td style='text-align: center;'><a href='/admin/offer/edit/$idoffer'>".CmsFactory::view()->fragment()->icon()->edit(18,18)."</a></td>
					<td>$idoffer</td>
					<td><a href='/admin/offer/edit/$idoffer'>$name</a></td>
					<td style='text-align: right;'>$price</td>
					<td>$availability</td>
					<td style='text-align: center;'>$validThrough</td>
					<td style='text-align: center;'>$dateCreated</td>
				</tr>";
		}
		$table .= '</tbody></table>';
		return $table;
	}
}
