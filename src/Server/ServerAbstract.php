<?php
namespace Plinct\Cms\Server;

use Plinct\Cms\Server\Type\HistoryServer;

abstract class ServerAbstract {

    protected static function response($response, $redirectPath = null) {
        if (isset($response['error'])) {
            var_dump([ "error" => [ "response" => $response ]]);
            die;
        } elseif($redirectPath) {
            return $redirectPath;
        } else {
            return filter_input(INPUT_SERVER, 'HTTP_REFERER');
        }
    }

    protected function setHistory($tableHasPart,$idHasPart, $action, $summary) {
        $history = new HistoryServer($tableHasPart, $idHasPart);
        $history->setSummary($summary);
        $history->register($action);
    }

    protected function setHistoryUpdateWithDifference($tableHasPart,$idHasPart, $dataOld, $dataNew) {
        $history = new HistoryServer($tableHasPart, $idHasPart);
        $history->setSummaryByDifference($dataNew, $dataOld);
        $history->register("UPDATE");
    }
}