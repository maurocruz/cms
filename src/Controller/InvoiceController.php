<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;

class InvoiceController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "properties" => "customer,provider", "orderBy" => "paymentDueDate desc" ];
        $params3 = array_merge($params2, $params);
        return Api::get("invoice", $params3);
    }

    public function edit(array $params): array {
        $params2 = [ "properties" => "customer, provider" ];
        $params3 = array_merge($params2, $params);
        return Api::get("invoice", $params3);
    }

    public function new($params = null) {
        return null;
    }
}