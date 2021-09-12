<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Intangible\OrderItem;

use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Table;

abstract class OrderItemAbstract
{
    /**
     * @var int
     */
    protected int $id;
    /**
     * @var int
     */
    protected int $referencesOrder;
    /**
     * @var int
     */
    protected int $orderedItem;
    /**
     * @var string
     */
    protected string $orderedItemType;
    /**
     * @var int
     */
    protected int $orderQuantity;
    /**
     * @var string
     */
    protected string $orderItemStatus;
    /**
     * @var int
     */
    protected int $sellerId;
    /**
     * @var string
     */
    protected string $sellerType;
    /**
     * @var int
     */
    protected int $orderId;
    /**
     * @var float
     */
    protected static float $TOTAL_BILL;

    use FormElementsTrait;

    /**
     * @param $data
     * @return array
     */
    protected function listOrderedItems($data): array
    {
        $idHasPart = ArrayTool::searchByValue($data['identifier'],"id","value");
        $sellerId = ArrayTool::searchByValue($data['seller']['identifier'], "id")['value'];
        $sellerType = $data['sellerType'];
        $discount = (float)$data['discount'];
        //
        $orderedItems = $data['orderedItem'] ?? null;
        $numberOfItems = $orderedItems ? count($orderedItems) : null;
        $quantityTotal = 0;
        $totalBill = 0;

        // TABLE
        $table = new Table(['class'=>'table-orderedItems']);

        // HEADERS
        $table->head(_("#"), ["style" =>"width: 30px;"])
            ->head(_("Type"), ["style" =>"width: 75px;"])
            ->head(_("Item"))
            ->head(_("Quantity"), ["style" =>"width: 75px;"])
            ->head(_("Unit price"), ["style" =>"width: 120px;"])
            ->head(_("Total price"), ["style" =>"width: 140px;"])
            ->head(_("Action"), ["style" =>"width: 45px;"]);

        // BODY
        if ($orderedItems) {
            foreach ($orderedItems as $key => $value) {
                $type = $value['orderedItem']['@type'];
                $name = $value['orderedItem']['name'];
                $idItem = ArrayTool::searchByValue($value['orderedItem']['identifier'],"id")['value'];
                $orderQuantity = $value['orderQuantity'];
                $price = isset($value['offer']['price']) ? (float)$value['offer']['price'] : null;
                $totalPrice = $price * $orderQuantity;
                $priceCurrency = $value['offer']['priceCurrency'] ?? null;

                // BODY CELLS
                $table->bodyCell($key+1)
                    ->bodyCell($type, ["style" =>"text-align: center;"])
                    ->bodyCell(sprintf('<a href="/admin/%s/%s?id=%s&item=%s">%s</a>',lcfirst($sellerType),lcfirst($type),$sellerId,$idItem,$name))
                    ->bodyCell($orderQuantity, ["style" =>"text-align: right;"])
                    ->bodyCell($priceCurrency." ".number_format($price,2,',','.'), ["style" =>"text-align: right;"])
                    ->bodyCell($priceCurrency." ".number_format($totalPrice,2,',','.'), ["style" =>"text-align: right;"])
                    ->bodyCell(self::deleteButton($value['idorderItem'],$idHasPart,$name))
                    ->closeRow();

                $quantityTotal += $orderQuantity;
                $totalBill += $totalPrice - $discount;
            }

            self::$TOTAL_BILL = $totalBill;

        } else {
            $table->bodyCell(_("No items founded!"), [ "colspan" => "7", "style" => "text-align: center;" ])->closeRow();
        }

        // FOOTER
        $table->foot(sprintf(_("%s Items"), "$numberOfItems"), [ "colspan" => "2" ])
            ->foot()
            ->foot((string)$quantityTotal)
            ->foot(sprintf(_("Discount: %s"), number_format($discount,2,',','.')))
            ->foot(number_format(self::$TOTAL_BILL,2,',','.'), [ "style" => "text-align: right;" ])
            ->foot();

        return $table->ready();
    }

    /**
     * @param $sellerHasOfferCatalog
     * @return array
     */
    protected function listSellerOfferedItems($sellerHasOfferCatalog): array
    {
        // NUMBER OF ITEMS
        $content[] = [ "tag" => "p", "content" => sprintf(_("%s items available in the catalog"), $sellerHasOfferCatalog['numberOfItems']) ];

        // TABLE
        $table = new Table();

        // TABLE HEAD
        $table->head(_("Select"), [ "style" => "width: 45px;"])
            ->headers([ _("Name"), _("Type") ])
            ->head(_("Price"), [ "style" => "width: 150px;"])
            ->head(_("Elegible duration"))
            ->head(_("Quantity"), [ "style" => "width: 100px;"]);

        // TABLE BODY
        foreach ($sellerHasOfferCatalog['itemListElement'] as $key => $value) {
            // VARS
            $item = $value['item'];
            $id = ArrayTool::searchByValue($item['itemOffered']['identifier'], "id")['value'];
            $name = $item['itemOffered']['name'];
            $type = $item['itemOffered']['@type'];
            $price = $item['priceCurrency'] . " " . number_format((float)$item['price'],2,',','.');
            $elegibleDuration = $item['elegibleDuration'];
            $hrefItem = sprintf("/admin/%s/%s?id=%s&item=%s", lcfirst($this->sellerType), lcfirst($type), $this->sellerId, $id);

            // REFERENCE ORDER
            $content[] = self::input("items[$key][referencesOrder]", "hidden", (string) $this->referencesOrder);

            // OFFER
            $content[] = self::input("items[$key][offer]", "hidden", $item['idoffer']);

            // OFFERED ITEM TYPE
            $content[] = self::input("items[$key][orderedItemType]", "hidden", $type );

            // TABLE ROW
            $table->bodyCell(self::checkbox("items[$key][orderedItem]", $id), [ "style" => "text-align: center;"])
                ->bodyCell($name, null, $hrefItem)
                ->bodyCell(_($type))
                ->bodyCell($price, ["style"=>"text-align: right;"])
                ->bodyCell($elegibleDuration)
                ->bodyCell(self::input("items[$key][orderQuantity]", "number", "1", [ "min" => "1" ]), [ "style" => "widht: 80px;"])
                ->closeRow();
        }

        $content[] = $table->ready();

        // SEND BUTTON
        $content[] = self::submitButtonSend();

        return self::form("/admin/orderItem/new", $content);
    }

    /**
     * @param $idorderItem
     * @param $idHasPart
     * @param $name
     * @return array
     */
    private static function deleteButton($idorderItem, $idHasPart,$name): array
    {
        return [ "tag" => "form", "attributes" => [ "style" => "background-color: inherit; text-align: center;", "method" => "post" ], "content" => [
                self::input("tableHasPart","hidden","order"),
                self::input("idHasPart","hidden",$idHasPart),
                self::input("id","hidden", $idorderItem),
                self::input("orderItemName","hidden", $name),
                self::submitButtonDelete("/admin/orderItem/erase", [ "style" => "width: 25px;"])
            ]];
    }
}
