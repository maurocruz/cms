<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\ServerAbstract;

class InvoiceServer extends  ServerAbstract {

    public function new($params) {
        unset($params['tableHasPart']);
        // REGISTER HISTORY IN ORDER REFERENCE
        $history = new HistoryServer('order', $params['referencesOrder']);
        $history->setSummary(sprintf("Added new invoice. payment: %s; due date: %s", $params['totalPaymentDue'], $params['paymentDueDate']));
        $history->register("CREATED");
        // RESPONSE
        return parent::response(Api::post('invoice',$params));
    }

    public function edit($params) {
        // REGISTER HISTORY IN ORDER REFERENCE
        $history = new HistoryServer('order', $params['referencesOrder']);
        // GET OLDER DATA
        $data = Api::get('invoice', [ "id" => $params['id'] ]);
        // COMPARE NEW DATA
        $history->setSummaryByDifference($params, $data[0]);
        // REGISTER HISTORY
        $history->register("UPDATE");
        // RESPONSE
        return parent::response(Api::put("invoice", $params));
    }

    public function erase($params) {
        // REGISTER HISTORY IN ORDER REFERENCE
        $history = new HistoryServer('order', $params['referencesOrder']);
        $history->setSummary(sprintf("Deleted invoice. payment: %s; due date: %s", $params['totalPaymentDue'], $params['paymentDueDate']));
        $history->register("DELETE");
        return parent::response(Api::delete('invoice', [ "id" => $params['id'] ]));
    }
}