<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Offer;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class OfferView extends OfferAbstract implements TypeViewInterface
{

	/**
   * @param ?array $value
   */
  public function index(?array $value)
  {
		$this->navbarOfferedBy($value);
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('offer')->setHasPart(self::$offeredById)->ready()
		);
  }

  /**
   * @param null $value
   */
  public function new($value = null)
  {
	  $this->navbarOfferedBy($value);
		//
	  CmsFactory::view()->addMain(
	    CmsFactory::view()->fragment()->box()->simpleBox(parent::formOffer(),_('New offer'))
	  );
  }

	public function edit(?array $data)
	{
		$value = $data[0];
		$offeredBy = $value['offeredBy'];
		$itemOffered = $value['itemOffered'];
		$tbItemOffered = ToolBox::typeBuilder($itemOffered);
		$itemOfferedName = $itemOffered['name'];
		$itemOfferedType = lcfirst($tbItemOffered->getType());
		$itemOfferedId = $tbItemOffered->getId();
		$this->navbarOfferedBy($offeredBy);

		CmsFactory::view()->addMain([
			CmsFactory::view()->fragment()->box()->simpleBox(parent::formOffer('edit',$value),_('Edit offer')),
			CmsFactory::view()->fragment()->box()->simpleBox("<p><a href='/admin/$itemOfferedType/edit/$itemOfferedId'>$itemOfferedName</a></p>",_('Item offered'))
			]
		);
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function editWithPartOf(array $data): array
	{
		// NEW OFFER
		$content[] = CmsFactory::view()->fragment()->box()->expandingBox(sprintf(_("Add new %s"), _("offer")), parent::formOffer());
		if ($data['offers'] === null) {
			$content[] = CmsFactory::view()->fragment()->miscellaneous()->message(_("No offers found"));
		} else {
			foreach ($data['offers'] as $key => $value) {
				$number = $key + 1;
				$content[] = CmsFactory::view()->fragment()->box()->simpleBox(parent::formOffer('edit', $value), _("Offer")." #$number");
			}
		}
		return $content;
	}
}
