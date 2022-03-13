<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\ServerAbstract;

class OrderItemServer extends ServerAbstract
{
    /**
     * @param array $params
     * @return mixed
     */
    public function new(array $params)
    {
        $numberOfOrderedItems = 0;
        $itemsOrdered = null;
        foreach ($params['items'] as $value) {
            if (isset($value['orderedItem'])) {
                parent::response(Api::post("orderItem", $value));
                $numberOfOrderedItems ++;
                $itemsOrdered[] = $value['orderedItemType'];
            }
        }
        // REGISTER HISTORY IN ORDER REFERENCE
        $history = new HistoryServer('order', $value['referencesOrder']);
        $history->setSummary(sprintf("Added new order items: %s;", implode(", ", $itemsOrdered)));
        $history->register("CREATED");
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    /**
     * @param $params
     * @return mixed|void
     */
    public function erase($params)
    {
        // HISTORY
        $history = new HistoryServer($params['tableHasPart'],$params['idHasPart']);
        $history->setSummary(sprintf("Deleted order item %s",$params['orderItemName']));
        $history->register("DELETE");
        // RESPONSE
        return parent::response(Api::delete('orderItem',["idorderItem"=>$params['idIsPartOf']]));
    }
}
