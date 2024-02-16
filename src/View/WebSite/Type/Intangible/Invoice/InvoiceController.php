<?php

declare(strict_types=1);

namespace Plinct\Cms\View\WebSite\Type\Intangible\Invoice;

use Plinct\Cms\Controller\CmsFactory;

class InvoiceController
{
    /**
     * @param $params
     * @return array
     */
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "customer,provider", "orderBy" => "paymentDueDate desc" ];
        $params3 = array_merge($params2, $params);
        return CmsFactory::request()->api()->get("invoice", $params3)->ready();
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params2 = [ "properties" => "customer, provider" ];
        $params3 = array_merge($params2, $params);
        return CmsFactory::request()->api()->get("invoice", $params3)->ready();
    }
}
