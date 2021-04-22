<?php
namespace Plinct\Cms\View\Types\Intangible\OrderItem;

use Plinct\Cms\View\Html\Page\ViewInterface;
use Plinct\Tool\ArrayTool;

class OrderItemView extends OrderItemWidget implements ViewInterface {
    public static $totalWithoutDiscount;
    public static $totalWithDiscount;

    public function index(array $data): array {
        return [];
    }

    public function new($data = null): array {
        return [];
    }

    public function edit(array $data): array {
        $this->referencesOrder = $data['idorder'];
        $this->orderedItem = $data['orderedItem'];
        $this->sellerId = ArrayTool::searchByValue($data['seller']['identifier'], "id")['value'];
        $this->sellerType = $data['seller']['@type'];
        return [
            parent::listOrderedItems($data),
            parent::divBoxExpanding(_("Include new item"), "OrderItem", [ parent::listSellerOfferedItems($data['seller']['hasOfferCatalog']) ])
        ];
    }

    public static function getTotalBill() {
        return self::$TOTAL_BILL;
    }
}
