<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class OrganizationController implements ControllerInterface
{
    public function index($params = null): array {
        $paramsSet = [ "format" => "ItemList", "properties" => "additionalType,dateModified", "orderBy" => "dateModified", "ordering" => "desc" ];
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        return Api::get("organization", $paramsGet);
    }
    
    public function edit(array $params): array {
        $data = Api::get("organization", [ "id" => $params['id'], "properties" => "*,address,location,contactPoint,member,image" ]);
        $data[0]['additionalType'] = str_replace(","," -> ",$data[0]['additionalType']);
        return $data;
    }
    
    public function new($params = null): bool {
        return true;
    }

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
     *  PRODUCT IS PROPERTY OF
     * @param array $params
     * @return array
     */
    public function product(array $params): array {
        $itemId = $params['item'] ?? null;
        if ($itemId) {
            $data = Api::get('product', [ "id" => $itemId, "properties" => "*,manufacturer,offers,image" ]);
        } else {
            $data = $this->edit($params);
            $data[0]['products'] = Api::get('product', ["format" => "ItemList", "properties" => "*", "manufacturer" => $params['id']]);
        }
        return $data;
    }

    public function order(array $params): array {
        $itemId = $params['item'] ?? null;
        $id = $params['id'];
        if ($itemId) {
            $data = Api::get('order', [ "id" => $itemId, "properties" => "*,customer,orderedItem,partOfInvoice,history" ]);
            $data[0]['orderedItem'] = Api::get("orderItem", [ "referencesOrder" => $itemId, "properties" => "*,orderedItem,offer" ]);
            $data[0]['seller'] = Api::get("organization", [ "id" => $id, "properties" => "hasOfferCatalog" ])[0];
            $data[0]['seller']['hasOfferCatalog'] = Api::get("offer", [ "format" => "ItemList", "offeredBy" => $id, "offeredByType" => "Organization", "properties" => "itemOffered", "availability" => "InStock", "where" => "`validThrough`>CURDATE()" ] );
        } else {
            $data = $this->edit($params);
            $data[0]['orders'] = Api::get('order', ["format" => "ItemList", "properties" => "*,customer,seller", "seller" => $id, "sellerType" => "Organization"]);
        }
        return $data;
    }

    public function saveSitemap() {
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
