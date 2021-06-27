<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class OrganizationController implements ControllerInterface {

    public function index($params = null): array {
        $paramsSet = [ "format" => "ItemList", "properties" => "name,additionalType,dateModified", "orderBy" => "dateModified", "ordering" => "desc" ];
        $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
        return Api::get("organization", $paramsGet);
    }
    
    public function edit(array $params): array {
        return Api::get("organization", [ "id" => $params['id'], "properties" => "*,address,location,contactPoint,member,image" ]);
    }
    
    public function new($params = null): bool {
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
            $data[0]['orders'] = (new OrderController())->payment();
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
