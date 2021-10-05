<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class OrganizationController
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        $paramsSet = [ "format" => "ItemList", "properties" => "name,additionalType,dateModified", "orderBy" => "dateModified", "ordering" => "desc" ];
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        return Api::get("organization", $paramsGet);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        return Api::get("organization", [ "id" => $params['id'], "properties" => "*,address,location,contactPoint,member,image" ]);
    }

    /**
     * @return bool
     */
    public function new(): bool
    {
        return true;
    }

    /**
     * SERVICE IS PART OF
     * @param array $params
     * @return array
     */
    public function service(array $params): array {
        $itemId = $params['item'] ?? null;
        if ($itemId) {
            $data = Api::get('service', [ "id" => $itemId, "properties" => "*,provider,offers" ]);
        } else {
            $data = $this->edit($params);
            $data[0]['services'] = Api::get('service', ["format" => "ItemList", "properties" => "*", "provider" => $params['id'], "orderBy" => "dateModified DESC" ]);
        }
        return $data;
    }

    /**
     *  PRODUCT IS PART OF
     * @param array $params
     * @return array
     */
    public function product(array $params): array
    {
        $id = $params['id'] ?? null;
        $itemId = $params['item'] ?? null;
        $action = $params['action'] ?? null;

        $data = Api::get("organization", [ "id" => $id, "properties" => "*,address,location,contactPoint,member,image" ]);

        if ($itemId) {
            $data[0]['action'] = "edit";
            $productData = Api::get('product', [ "id" => $itemId, "properties" => "*,manufacturer,offers,image" ]);
            $data[0]['product'] = $productData[0];
        } else {
            if ($action == 'new') {
                $data[0]['action'] = 'new';
            } else {
                $data[0]['products'] = Api::get('product', ["format" => "ItemList", "properties" => "*", "manufacturer" => $id]);
            }
        }
        return $data[0];
    }

    /**
     * @param array $params
     * @return array
     */
    public function order(array $params): array
    {
        // PARAMS
        $itemId = $params['item'] ?? null;
        $id = $params['id'];
        $customerName = $params['customerName'] ?? null;
        $action = filter_input(INPUT_GET, 'action');

        // ITEM
        if ($itemId):
            $data = (new OrderController())->editWithPartOf($itemId, $id);

        // PAYMENT
        elseif($action == "payment"):
            $data = $this->edit($params);
            $data[0]['orders'] = (new OrderController())->payment($id);

        // EXPIRED
        elseif($action == "expired"):
            $data = $this->edit($params);
            $data[0]['orders'] = (new OrderController())->expired();

        // LIST
        else:
            $data = $this->edit($params);
            $data[0]['orders'] = (new OrderController())->indexWithPartOf($customerName, $id);

        endif;

        return $data;
    }

    public function saveSitemap()
    {
        $dataSitemap = null;
        $params = [ "properties" => "image,dateModified", "orderBy" => "dateModified desc" ];
        $data = Api::get("organization", $params);
        foreach ($data as $value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/organization/$id",
                "lastmod" => DateTime::formatISO8601($value['dateModified']),
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-organization.xml"))->saveSitemap($dataSitemap);
    }
}
