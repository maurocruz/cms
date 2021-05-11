<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;

class HistoryServer {
    private $tableHasPart;
    private $idHasPart;
    private $action;
    private $summary;
    private $userId;

    public function __construct($tableHasPart, $idHasPart) {
        $this->tableHasPart = $tableHasPart;
        $this->idHasPart = $idHasPart;
        $this->userId = App::getUserId();
    }

    public function setSumaryByDifference(array $params, array $data) {
        $text = '';
        $data = array_filter($data, function ($var) { return(!is_array($var));  });
        $diff = array_diff($params, $data);
        foreach ($diff as $key => $value) {
            $text .= $key.": $data[$key] to $value; ";
        }
        $this->summary = $text;
    }

    public function register($action, $summary = null) {
        $this->action = $action;
        $this->summary = $summary ?? $this->summary;
        $params = $this->getParams();
        return Api::post("history", $params);
    }

    private function getParams(): array {
        $params['tableHasPart'] = $this->tableHasPart;
        $params['idHasPart'] = $this->idHasPart;
        $params['action'] = $this->action;
        $params['summary'] = $this->summary;
        $params['user'] = $this->userId;
        return $params;
    }
}