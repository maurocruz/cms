<?php


namespace Plinct\Cms\Controller;


use Plinct\Api\Type\Invoice;

class InvoiceController implements ControllerInterface
{

    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "customer,provider", "orderBy" => "paymentDueDate desc" ];
        $params3 = array_merge($params2, $params);
        return (new Invoice())->get($params3);
    }

    public function edit(array $params): array
    {
        $params2 = [ "properties" => "customer, provider" ];
        $params3 = array_merge($params2, $params);
        return (new Invoice())->get($params3);
    }

    public function new($params = null)
    {
        return null;
    }
}