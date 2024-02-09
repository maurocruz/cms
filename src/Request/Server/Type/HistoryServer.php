<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Api\Api;

class HistoryServer
{
  private string $tableHasPart;
  private string $idHasPart;
  private string $action;
  private string $summary;
  private string $userId;

  /**
   * HistoryServer constructor.
   * @param $tableHasPart
   * @param $idHasPart
   */
  public function __construct($tableHasPart, $idHasPart)
  {
    $this->tableHasPart = $tableHasPart;
    $this->idHasPart = $idHasPart;
    $this->userId = CmsFactory::request()->user()->userLogged()->getIduser();
  }

  /**
   * @param mixed $summary
   */
  public function setSummary($summary): void
  {
    $this->summary = $summary;
  }

  /**
   * @param array $params
   * @param array $data
   */
  public function setSummaryByDifference(array $params, array $data)
  {
    $dataOld = $data[0] ?? $data;
    $text = '';
    $dataFiltered = array_filter($dataOld, function ($var) { return(!is_array($var));  });
    $diff = array_diff($params, $dataFiltered);
    foreach ($diff as $key => $value) {
        $text .= $key.": $dataFiltered[$key] to $value; ";
    }
    $this->summary = $text;
  }

/**
 * @param $action
 * @param null $summary
 * @return Api
 */
  public function register($action, $summary = null): Api
  {
    $this->action = $action;
    $this->summary = $summary ?? $this->summary;
    $params = $this->setParams();
    return CmsFactory::request()->api()->post("history", $params);
  }

  /**
   * @return array
   */
  private function setParams(): array
  {
    $params['tableHasPart'] = $this->tableHasPart;
    $params['idHasPart'] = $this->idHasPart;
    $params['action'] = $this->action;
    $params['summary'] = $this->summary;
    $params['user'] = $this->userId;
    return $params;
  }
}
