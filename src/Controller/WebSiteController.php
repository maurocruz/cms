<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\Sitemap;
use Plinct\Tool\ArrayTool;

class WebSiteController implements ControllerInterface
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        return Api::get('webSite',['format'=>'ItemList','properties'=>'name,url']);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['id'] ?? null;
        $data = Api::get('webSite',['id'=>$id, 'properties'=>'hasPart']);

        $idSite = ArrayTool::searchByValue($data[0]['identifier'],'id','value');
        $data[0]['hasPart'] = Api::get('webPage',['isPartOf'=>$idSite, 'orderBy'=>'dateModified desc']);

        return $data;
    }

    /**
     * @param null $params
     * @return array
     */
    public function new($params = null): array
    {
        return [];
    }

    /**
     * @param null $params
     * @return array
     */
    public function webPage($params = null): array
    {
        // vars
        $id = $params['id'];
        $action = $params['action'] ?? null;
        $item = $params['item'] ?? null;

        // ITEM
        if ($item) {
            $dataWebPage = Api::get('webPage',['id'=>$item,'properties'=>'*,isPartOf,hasPart']);
            return (array)$dataWebPage[0];
        }

        // ALL and NEW
        $dataWebSite = Api::get('webSite',['id'=>$id,'properties'=>'*']);
        $data = (array)$dataWebSite[0];
        $idwebSite = ArrayTool::searchByValue($data['identifier'],'id','value');

        // list all webpages if not isset action
        if (!$action) {
            $data['hasPart'] = Api::get('webPage', ['format'=>'ItemList','isPartOf'=>$idwebSite,'properties'=>'isPartOf,dateModified','orderBy'=>'dateModified desc']);

        } elseif ($action == 'sitemap') {
            $data['sitemaps'] = (new Sitemap())->getSitemaps();
        }
        // response
        return $data;
    }
}
