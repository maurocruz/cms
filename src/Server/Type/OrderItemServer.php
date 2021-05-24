<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class OrderItemServer {

    public static function new(array $params) {
        foreach ($params['items'] as $value) {
            if (isset($value['orderedItem'])) {
                Api::post("orderItem", $value);
            }
        }
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}
