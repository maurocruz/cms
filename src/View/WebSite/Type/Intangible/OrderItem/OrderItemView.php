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

	public function index(?array $data, array $queryParams = null): void
	{
	}

	public function new(?array $data, array $queryParams = null): void
	{
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
  public function edit(?array $data, array $queryParams = null): void
  {
		$typeBuilderOrder = ToolBox::typeBuilder($data);
    $this->orderItemNumber = $typeBuilderOrder->getId();
    $this->orderedItem = $data['orderedItem'] ?? null;
	  $seller = $data['seller'];
		$typeBuilderSeller = ToolBox::typeBuilder($seller);
    $this->sellerId = $typeBuilderSeller->getId();
    $this->sellerType = $seller['@type'];
		CmsFactory::view()->addMain([
			parent::listOrderedItems($data),
	    CmsFactory::view()->fragment()->box()->expandingBox(_("Include new item"), parent::listSellerOfferedItems($seller['hasOfferCatalog']))
	  ]);
  }

  /**
   * @return float
   */
  public static function getTotalBill(): float
  {
    return self::$TOTAL_BILL;
  }
}
