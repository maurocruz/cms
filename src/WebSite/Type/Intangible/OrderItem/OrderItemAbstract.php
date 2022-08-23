<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\OrderItem;

use Plinct\Cms\CmsFactory;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\ToolBox;
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
    protected static float $TOTAL_BILL = 0;


    /**
     * @param $data
     * @return array
     */
    protected function listOrderedItems($data): array
    {
        $idHasPart = ArrayTool::searchByValue($data['identifier'],"id","value");
        $tableHasPart = lcfirst($data['@type']);
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
                $orderQuantity = (float)$value['orderQuantity'];
                $price = isset($value['offer']['price']) ? (float)$value['offer']['price'] : null;
                $totalPrice = $price * $orderQuantity;
                $priceCurrency = $value['offer']['priceCurrency'] ?? null;

                // BODY CELLS
                $table->bodyCell($key+1)
                    ->bodyCell($type, ["style" =>"text-align: center;"])
                    ->bodyCell(sprintf('<a href="/admin/%s/%s?id=%s&item=%s">%s</a>',lcfirst($sellerType),lcfirst($type),$sellerId,$idItem,$name))
                    ->bodyCell($orderQuantity, ["style" =>"text-align: right;"])
                    ->bodyCell($priceCurrency." ".($price ? number_format($price,2,',','.') : "ND"), ["style" =>"text-align: right;"])
                    ->bodyCell($priceCurrency." ".number_format($totalPrice,2,',','.'), ["style" =>"text-align: right;"])
                    ->bodyCell(CmsFactory::response()->fragment()->button()->buttonDelete($value['idorderItem'],"orderItem",$idHasPart,$tableHasPart, ['class'=>'form-orderedItem-delete-button']))
                    ->closeRow();

                $quantityTotal += $orderQuantity;
                $totalBill += $totalPrice - $discount;
            }

            self::$TOTAL_BILL = $totalBill;

        } else {
            $table->bodyCell(_("No items found!"), [ "colspan" => "7", "style" => "text-align: center;" ])->closeRow();
        }

        // FOOTER
        $table->foot(sprintf(_("%s items"), "$numberOfItems"), [ "colspan" => "2" ])
            ->foot()
            ->foot((string)$quantityTotal)
            ->foot(sprintf(_("Discount: %s"), number_format($discount,2,',','.')))
            ->foot(number_format(self::$TOTAL_BILL,2,',','.'), [ "style" => "text-align: right;" ])
            ->foot();

        return ['tag'=>'div','attributes'=>['style'=>'max-width: 100%; overflow-x: scroll;'], 'content'=> $table->ready() ];
    }

    /**
     * @param $sellerHasOfferCatalog
     * @return array
     */
    protected function listSellerOfferedItems($sellerHasOfferCatalog): array
    {
        $form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao']);
        $form->action("/admin/orderItem/new")->method("post");
        // number of items
        $form->content("<p>" . sprintf(_("%s items available in the catalog"), $sellerHasOfferCatalog['numberOfItems']) . "</p>");

        $table = new Table();
        $table->head(_("Select"), [ "style" => "width: 45px;"])
            ->headers([ _("Name"), _("Type") ])
            ->head(_("Price"), [ "style" => "width: 150px;"])
            ->head(_("Elegible duration"))
            ->head(_("Quantity"), [ "style" => "width: 80px;"]);

        foreach ($sellerHasOfferCatalog['itemListElement'] as $key => $value) {
            $item = $value['item'];
            $id = ToolBox::searchByValue($item['itemOffered']['identifier'], "id")['value'];
            $name = $item['itemOffered']['name'];
            $type = $item['itemOffered']['@type'];
            $price = $item['priceCurrency'] . " " . number_format((float)$item['price'],2,',','.');
            $elegibleDuration = $item['elegibleDuration'];
            $hrefItem = sprintf("/admin/%s/%s?id=%s&item=%s", lcfirst($this->sellerType), lcfirst($type), $this->sellerId, $id);

            // REFERENCE ORDER
            $form->input("items[$key][referencesOrder]", (string) $this->referencesOrder, "hidden");
            // OFFER
            $form->input("items[$key][offer]", $item['idoffer'], "hidden");
            // OFFERED ITEM TYPE
            $form->input("items[$key][orderedItemType]", $type, "hidden");

            // TABLE ROW
            $table->bodyCell("<input name='items[$key][orderedItem]' type='checkbox' value='$id' >", [ "style" => "text-align: center;"])
                ->bodyCell($name, null, $hrefItem)
                ->bodyCell(_($type))
                ->bodyCell($price, ["style"=>"text-align: right;"])
                ->bodyCell($elegibleDuration)
                ->bodyCell("<input name='items[$key][orderQuantity]' type='number' value='1' min='1' style='width: 80px;'>")
                ->closeRow();
        }

        $form->content($table->ready());

        $form->submitButtonSend();

        return ['tag'=>'div','attributes'=>['style'=>'max-width: 100%; overflow-x: scroll;'], 'content'=> $form->ready() ];
    }
}
