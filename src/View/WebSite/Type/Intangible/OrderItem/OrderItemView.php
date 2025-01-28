<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\OrderItem;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;
use Plinct\Tool\ToolBox;

class OrderItemView extends OrderItemAbstract implements TypeViewInterface
{
  /**
   * @var float
   */
  public static float $totalWithoutDiscount;
  /**
   * @var float
   */
  public static float $totalWithDiscount;

	public function index(?array $value): bool
	{
		return true;
	}

	public function new(?array $value): bool
	{
		return true;
	}

	/**
	 * @param ?array $data
	 * @return array
	 */
  public function edit(?array $data): array
  {
		$typeBuilderOrder = ToolBox::typeBuilder($data);
    $this->orderItemNumber = $typeBuilderOrder->getId();
    $this->orderedItem = $data['orderedItem'] ?? null;
	  $seller = $data['seller'];
		$typeBuilderSeller = ToolBox::typeBuilder($seller);
    $this->sellerId = $typeBuilderSeller->getId();
    $this->sellerType = $seller['@type'];
		return [
			parent::listOrderedItems($data),
	    CmsFactory::view()->fragment()->box()->expandingBox(_("Include new item"), parent::listSellerOfferedItems($seller['hasOfferCatalog']))
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
