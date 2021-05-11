<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class InvoiceServer {

    public function edit($params) {
        // REGISTER HISTORY IN ORDER REFERENCE
        $history = new HistoryServer('order', $params['referencesOrder']);
        // GET OLDER DATA
        $data = Api::get('invoice', [ "id" => $params['id'] ]);
        // COMPARE NEW DATA
        $history->setSumaryByDifference($params, $data[0]);
        // REGISTER HISTORY
        $history->register("UPDATE");
        Api::put("invoice", $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}