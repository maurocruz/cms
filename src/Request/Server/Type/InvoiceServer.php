<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Server\ServerAbstract;

class InvoiceServer
{
	/**
	 * @param array $params
	 * @return array
	 */
  public function new(array $params): array
  {
    unset($params['tableHasPart']);
    // REGISTER HISTORY IN ORDER REFERENCE
    $history = new HistoryServer('order', $params['referencesOrder']);
    $history->setSummary(sprintf("Added new invoice. payment: %s; due date: %s", $params['totalPaymentDue'], $params['paymentDueDate']));
    $history->register("CREATED")->ready();
	  // SET NEW DATE IN REFERENCE ORDER
	  CmsFactory::request()->api()->put('order',['idorder'=>$params['referencesOrder'],'dateModified'=>date('Y-m-d H:i:s')])->ready();
    // RESPONSE
    return $params;
  }

	/**
	 * @param array $params
	 * @return mixed|void
	 */
  public function edit(array $params): array
  {
    // REGISTER HISTORY IN ORDER REFERENCE
    $history = new HistoryServer('order', $params['referencesOrder']);
    // GET OLDER DATA
    $data = CmsFactory::request()->api()->get('invoice', [ "idinvoice" => $params['idinvoice'] ])->ready();
    // COMPARE NEW DATA
    $history->setSummaryByDifference($params, $data[0]);
    // REGISTER HISTORY
    $history->register("UPDATE")->ready();
		// SET NEW DATE IN REFERENCE ORDER
	  CmsFactory::request()->api()->put('order',['idorder'=>$params['referencesOrder'],'dateModified'=>date('Y-m-d H:i:s')])->ready();
    // RESPONSE
    return $params;
  }

	/**
	 * @param array $params
	 * @return string
	 */
  public function erase(array $params): string
  {
    // REGISTER HISTORY IN ORDER REFERENCE
    $history = new HistoryServer('order', $params['referencesOrder']);
    $history->setSummary(sprintf("Deleted invoice. payment: %s; due date: %s", $params['totalPaymentDue'], $params['paymentDueDate']));
    $history->register("DELETE")->ready();
    CmsFactory::request()->api()->delete('invoice', [ "idinvoice" => $params['idinvoice'] ])->ready();
	  // SET NEW DATE IN REFERENCE ORDER
	  CmsFactory::request()->api()->put('order',['idorder'=>$params['referencesOrder'],'dateModified'=>date('Y-m-d H:i:s')])->ready();
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }
}
