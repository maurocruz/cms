<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class OfferServer {

    public function new($params) {
        Api::post('offer',$params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}