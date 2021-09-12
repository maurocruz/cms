<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\OrderItem;

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
        $this->referencesOrder = (int) $data['idorder'];
        $this->orderedItem = (int) $data['orderedItem'];
        $this->sellerId = (int) ArrayTool::searchByValue($data['seller']['identifier'], "id")['value'];
        $this->sellerType = $data['seller']['@type'];

        return [
            parent::listOrderedItems($data),
            parent::divBoxExpanding(_("Include new item"), "OrderItem", [ parent::listSellerOfferedItems($data['seller']['hasOfferCatalog']) ])
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
