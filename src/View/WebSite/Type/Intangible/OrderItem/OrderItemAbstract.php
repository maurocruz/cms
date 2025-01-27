<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible\OrderItem;

use Plinct\Cms\CmsFactory;
use Plinct\Tool\ToolBox;
use Plinct\Web\Element\Table;

abstract class OrderItemAbstract
{
  /**
   * @var string
   */
  protected string $id;
  /**
   * @var string
   */
  protected string $referencesOrder;
  /**
   * @var ?array
   */
  protected ?array $orderedItem = null;
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
   * @var string
   */
  protected string $sellerId;
  /**
   * @var string
   */
  protected string $sellerType;
  /**
   * @var string
   */
  protected string $orderId;
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
    $idorder = $this->referencesOrder;
    $discount = (float) $data['discount'];
    $orderedItems = $data['orderedItem'] ?? null;
		$acceptedOffer = $data['acceptedOffer'] ?? null;
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
				$orderItem = ToolBox::typeBuilder($value);
				$idordemItem = $orderItem->getId();
				$orderedItem = $value['orderedItem'];
				$typeBuilderOrdereItem = ToolBox::typeBuilder($orderedItem);
        $type = $orderedItem['@type'];
        $name = $orderedItem['name'];
        $isOrderedItem = $typeBuilderOrdereItem->getId();
        $orderQuantity = (float)$value['orderQuantity'];
        $price = isset($acceptedOffer[$key]['price']) ? (float) $acceptedOffer[$key]['price'] : null;
        $totalPrice = $price * $orderQuantity;
        $priceCurrency = $acceptedOffer[$key]['priceCurrency'] ?? null;
				$priceFormated = $price ? ToolBox::NumberFormatterCurrency($priceCurrency)->format($price) : null;
				$totalBillFormatter = $priceCurrency ? ToolBox::NumberFormatterCurrency($priceCurrency)->format($totalPrice) : null;

        // BODY CELLS
        $table->bodyCell($key+1)
          ->bodyCell($type, ["style" =>"text-align: center;"])
          ->bodyCell(sprintf('<a href="/admin/%s/edit/%s">%s</a>',lcfirst($type),$isOrderedItem,$name))
          ->bodyCell($orderQuantity, ["style" =>"text-align: right;"])
          ->bodyCell($priceFormated, ["style" =>"text-align: right;"])
          ->bodyCell($totalBillFormatter, ["style" =>"text-align: right;"])
          ->bodyCell(CmsFactory::view()->fragment()->buttons()->buttonDelete($idordemItem,"orderItem",$idorder,"order",['class'=>'form-orderItem']))
          ->closeRow();

        $quantityTotal += $orderQuantity;
        $totalBill += $totalPrice;
      }

      self::$TOTAL_BILL = $totalBill - $discount;

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
    $form = CmsFactory::view()->fragment()->form(['class'=>'form-basic']);
    $form->action("/admin/orderItem/new")->method("post");
    // number of items
    $form->content("<p>" . sprintf(_("%s items available in the catalog"), $sellerHasOfferCatalog['numberOfItems']) . "</p>");

    $table = new Table();
    $table->head(_("Select"), [ "style" => "width: 45px;"])
      ->headers([ _("Name"), _("Type") ])
      ->head(_("Price"), [ "style" => "width: 150px;"])
      ->head(_("Elegible duration"))
      ->head(_("Quantity"), [ "style" => "width: 80px;"]);

		if ($sellerHasOfferCatalog['numberOfItems'] == '0') {
			$table->bodyCell(_("No items available!"), ['colspan'=>'6','style'=>'text-align: center;'])->closeRow();
		} else {
			foreach ($sellerHasOfferCatalog['itemListElement'] as $key => $value) {
				$item = $value['item'];
				$typeBuilder = ToolBox::typeBuilder($item);
				$idoffer = $typeBuilder->getId();
				$itemOffered = $item['itemOffered'];
				$typeBuilderItemOffered = ToolBox::typeBuilder($itemOffered);
				$itemOrderedThing = $typeBuilderItemOffered->getPropertyValue('idthing');
				$name = $itemOffered['name'];
				$type = $itemOffered['@type'];
				$price = ToolBox::NumberFormatterCurrency($item['priceCurrency'])->format($item['price']);
				$eligibleDuration = $item['eligibleDuration'] ?? null;
				$hrefItem = sprintf("/admin/offer/edit/%s", $idoffer);

				// REFERENCE ORDER
				$form->input("items[$key][referencesOrder]", $this->referencesOrder, "hidden");
				// OFFER
				//$form->input("items[$key][offer]", $idoffer, "hidden");
				// OFFERED ITEM TYPE
				$form->input("items[$key][orderedItem]", $itemOrderedThing, "hidden");

				// TABLE ROW
				$table->bodyCell("<input name='items[$key][offer]' type='checkbox' value='$idoffer' >", ["style" => "text-align: center;"])
					->bodyCell($name, null, $hrefItem)
					->bodyCell(_($type))
					->bodyCell($price, ["style" => "text-align: right;"])
					->bodyCell($eligibleDuration)
					->bodyCell("<input name='items[$key][orderQuantity]' type='number' value='1' min='1' style='width: 80px;'>")
					->closeRow();
			}
		}

    $form->content($table->ready());

    $form->submitButtonSend();

    return ['tag'=>'div','attributes'=>['style'=>'max-width: 100%; overflow-x: scroll;'], 'content'=> $form->ready() ];
  }
}
