<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\Intangible\Offer;

use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\ArrayTool;

class OfferView extends OfferWidget
{
  /**
   *
   */
  private function navbarOffer()
  {
    CmsFactory::webSite()->navbar(_("Offer"), [
      "/admin/offer" => CmsFactory::response()->fragment()->icon()->home(),
      "/admin/offer/new" => CmsFactory::response()->fragment()->icon()->plus()
    ]);
  }

  /**
   * @param array $data
   */
  public function index(array $data)
  {
    $this->navbarOffer();

    $table = CmsFactory::response()->fragment()->listTable();
    $table->caption(sprintf(_("List of %s"), _("offers")));
    $table->labels(_("Price"), _("Valid through"), _("Item offered"), _("Item offered type"));
    $table->rows($data['itemListElement'], ['price','validThrough','itemOffered:name','itemOfferedType']);
    CmsFactory::webSite()->addMain($table->ready());
  }

  /**
   * @param array $data
   * @return array
   */
  public function editWithPartOf(array $data): array
  {
    $this->setOfferedBy($data);

    $this->tableHasPart = lcfirst($data['@type']);
    $this->idHasPart = ArrayTool::searchByValue($data['identifier'], "id")['value'];

    // NEW OFFER
    $content[] = CmsFactory::response()->fragment()->box()->expandingBox(sprintf(_("Add new %s"), _("offer")), parent::formOffer());

    if ($data['offers'] === null) {
      $content[] = CmsFactory::response()->fragment()->miscellaneous()->message(_("No offers found"));

    } else {
      foreach ($data['offers'] as $key => $value) {
        $number = $key + 1;
        $content[] = CmsFactory::response()->fragment()->box()->simpleBox(self::formOffer($value), _("Offer")." #$number");
      }
    }

    return $content;
  }

  /**
   * @param null $data
   */
  public function new($data = null)
  {
    $this->tableHasPart = lcfirst($data['@type']);
    $this->idHasPart = ArrayTool::searchByValue($data['identifier'], "id")['value'];
		CmsFactory::webSite()->addMain(
			CmsFactory::response()->fragment()->box()->expandingBox(sprintf(_("Add new %s"), _("offer")), parent::formOffer())
		);
  }

  /**
   * @param $tableHasPart
   * @param $idHasPart
   * @param $data
   * @return array
   */
  public function getForm($tableHasPart, $idHasPart, $data): array
  {
    $content = null;
    $this->tableHasPart = $tableHasPart;
    $this->idHasPart = $idHasPart;
    if ($data) {
      foreach ($data as $value) {
        $content[] = self::formOffer($value);
      }
    } else {
      $content[] = ["New: ", self::formOffer()];
    }
    return $content;
  }
}
