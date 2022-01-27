<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Person;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PersonController
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("person", $params3);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params = array_merge($params, [ "properties" => "*,contactPoint,address,image" ]);
        return Api::get("person", $params);
    }

    /**
     * @param null $params
     * @return bool
     */
    public function new($params = null): bool
    {
        return true;
    }

    /**
     * @param null $params
     * @return array
     */
    public function service($params = null): array
    {
        $id = $params['id'] ?? null;
        $action = $params['action'] ?? null;
        $item = $params['item'] ?? null;

        if ($item) {
            $data = Api::get('service',['provider'=>$id,'providerType'=>'person','id'=>$item,'properties'=>'provider,offer']);
        } else {
            $data = Api::get('person', ['id' => $id]);
        }

        if ($action == 'new') {
            $data[0]['action'] = "new";
        } else {
            $data[0]['services'] = Api::get('service', ['format' => 'ItemList', 'provider' => $id, 'providerType' => 'person','orderBy'=>'dateModified desc']);
        }

        return $data[0];
    }

    /**
     * PRODUCT BY PERSON
     *
     * @param null $params
     * @return mixed
     */
    public function product($params = null)
    {
        $id = $params['id'] ?? null;
        $action = $params['action'] ?? null;

        // LIST PRODUCTS BY PERSON
        $data = Api::get('person',['id'=>$id]);

        if($action=='new') {
            $data[0]['action'] = 'new';
        } else {
            $data[0]['products'] = Api::get('product', ['format' => 'ItemList', 'manufacturer' => $id, 'manufacturerType' => 'person', 'orderBy' => 'dateModified desc']);
        }
        return $data[0];
    }

    /**
     *
     */
    public function saveSitemap()
    {
        $dataSitemap = null;
        $params = [ "orderBy" => "dateModified desc", "properties" => "dateModified,image" ];
        $data = Api::get("person", $params);
        $loc = App::getURL() ."/t/Person/";
        foreach ($data as $value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $lastmod = DateTime::formatISO8601($value['dateModified']);
            $dataSitemap[] = [
                "loc" => $loc.$id,
                "lastmod" => $lastmod,
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-person.xml"))->saveSitemap($dataSitemap);
    }
}
