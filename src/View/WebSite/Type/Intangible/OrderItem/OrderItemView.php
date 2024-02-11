<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\Intangible\OrderItem;

use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\ArrayTool;

class OrderItemView extends OrderItemAbstract
{
  /**
   * @var float
   */
  public static float $totalWithoutDiscount;
  /**
   * @var float
   */
  public static float $totalWithDiscount;

  /**
   * @param array $data
   * @return array
   */
  public function edit(array $data): array
  {
    $this->referencesOrder = $data['idorder'];
    $this->orderedItem = $data['orderedItem'];
    $this->sellerId = ArrayTool::searchByValue($data['seller']['identifier'], "id")['value'];
    $this->sellerType = $data['seller']['@type'];

    return [
      parent::listOrderedItems($data),
      CmsFactory::response()->fragment()->box()->expandingBox(_("Include new item"), parent::listSellerOfferedItems($data['seller']['hasOfferCatalog']))
    ];
  }

  /**
   * @return float
   */
  public static function getTotalBill(): float
  {
    return self::$TOTAL_BILL;
  }
}
