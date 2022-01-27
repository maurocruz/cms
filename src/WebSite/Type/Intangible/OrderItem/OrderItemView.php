<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\OrderItem;

use Plinct\Cms\WebSite\Fragment\Fragment;
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
            Fragment::box()->expandingBox(_("Include new item"), parent::listSellerOfferedItems($data['seller']['hasOfferCatalog']))
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
