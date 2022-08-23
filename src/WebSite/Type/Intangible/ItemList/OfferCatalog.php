<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\ItemList;

use Plinct\Cms\Response\View\Fragment\Fragment;
use Plinct\Web\Element\Table;

class OfferCatalog implements ItemListInterface
{
    /**
     * @param array $itemList
     * @return array
     */
    public function ItemList(array $itemList): array
    {
        if ($itemList['@type'] == "ItemList") {
            // VAR
            $numberOfItems = $itemList['numberOfItems'];
            $itemListElement = $itemList['itemListElement'];
            $showing = sprintf(_("Showing %s items!"), $numberOfItems);
            // TABLE
            $table = new Table();
            // caption
            $table->caption($showing);

            if (!$itemListElement) {
                $table->bodyCell(_("No items found!"))->closeRow();
            }
            // ready
            return $table->ready();

        } else {
            return Fragment::miscellaneous()->message(_("No ItemList object found!"));
        }
    }
}