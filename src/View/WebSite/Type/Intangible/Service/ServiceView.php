<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\Service;

use Plinct\Cms\CmsFactory;
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
			$provider = $data['provider'];
			$offers = $data['offers'];
			$TBProvider = ToolBox::typeBuilder($provider);
			$this->provider = $TBProvider->getPropertyValue('idthing');
			if($TBProvider->getType() == 'Organization') {
				Organization::navbarIndex();
				Organization::navbarEdit($TBProvider->getValue('name'), $TBProvider->getId(), $this->provider);
			}
			parent::navbarIndex();
			parent::navbarEdit($data['name']);
			// EDIT SERVICE
			CmsFactory::view()->addMain(self::serviceForm("edit", $data));
			// OFFERS
			$offerList = '<table><thead><tr><th>#</th><th>idoffer</th><th>'._('Name').'</th><th>'._('Availability').'</th><th>'._('Valid through').'</th><th>'._('Date created').'</th></tr></thead><tbody>';
			foreach ($offers as $key => $offer) {
				$tbOffer = ToolBox::typeBuilder($offer);
				$name = $offer['name'];
				$idoffer = $tbOffer->getId();
				$dateCreated = $tbOffer->getPropertyValue('dateCreated');
				$availability = $offer['availability'];
				$validThrough = $offer['validThrough'];
				$offerList .= "<tr><td>".($key+1)."</td><td>$idoffer</td><td><a href='/admin/offer/edit/$idoffer'>$name</a></td><td>$availability</td><td>$validThrough</td><td>$dateCreated</td></tr>";
			}
			$offerList .= '</tbody></table>';
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->box()->simpleBox($offerList,_('Offers'))
			);
			//CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox( _("Offer"), (new OfferView())->editWithPartOf($data)));
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
