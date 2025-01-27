<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Service;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\Offer\OfferView;
use Plinct\Cms\View\WebSite\Type\Organization\Organization;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class ServiceView extends ServiceAbstract implements TypeViewInterface
{
	/**
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value)
	{
		$tb = ToolBox::typeBuilder($value);
		$this->provider = $tb->getPropertyValue('idthing');
		if ($tb->getType() == 'Organization') {
			Organization::navbarIndex();
			Organization::navbarEdit($tb->getValue('name'), $tb->getId(), $this->provider);
		}
		parent::navbarIndex();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('service')->setHasPart($this->provider)->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @return void
	 */
	public function edit(?array $data)
	{
		if (empty($data)) {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->message());
		} else {
			$TBService = ToolBox::typeBuilder($data);
			$serviceThing = $TBService->getIdthing();
			$provider = $data['provider'];
			$offers = $data['offers'];
			$TBProvider = ToolBox::typeBuilder($provider);
			$this->provider = $TBProvider->getIdthing();
			if($TBProvider->getType() == 'Organization') {
				Organization::navbarIndex();
				Organization::navbarEdit($TBProvider->getValue('name'), $TBProvider->getId(), $this->provider);
			}
			parent::navbarIndex();
			parent::navbarEdit($data['name']);
			// EDIT SERVICE
			CmsFactory::view()->addMain([
				self::serviceForm("edit", $data),
				// OFFERS
				CmsFactory::view()->fragment()->box()->expandingBox(_('Offers'), [
					CmsFactory::view()->fragment()->box()->expandingBox(_('New offer'), OfferView::newOffer($serviceThing, $this->provider)),
					OfferView::listOffers($offers)
				])
			]);
		}
	}

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function new(?array $value)
	{
		if (!empty($value)) {
			$tbProvider = ToolBox::typeBuilder($value);
			$this->provider = $tbProvider->getPropertyValue('idthing');
		}
		// NAVBAR
		$this->navbarIndex();
		// FORM
		CmsFactory::view()->addMain(parent::serviceForm());
	}
}
