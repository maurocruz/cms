<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebSite;

use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\Sitemap;
use Plinct\Cms\WebSite\Type\ControllerInterface;
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
        $id = $params['id'] ?? $params['idwebSite'] ?? null;
        $data = Api::get('webSite',['id'=>$id, 'properties'=>'hasPart']);
        if (isset($data[0]['identifier'])) {
            $idSite = ArrayTool::searchByValue($data[0]['identifier'], 'id', 'value');
            $data[0]['hasPart'] = Api::get('webPage', ['isPartOf' => $idSite, 'orderBy' => 'dateModified desc']);
        }

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
        $search = $params['q'] ?? $params['search'] ?? null;

        // ITEM
        if ($item) {
            $dataWebPage = Api::get('webPage',['id'=>$item,'properties'=>'*,isPartOf,identifier'])[0];

            $idwebPage = $dataWebPage['idwebPage'];
            $dataWebPage['hasPart'] = Api::get('webPageElement', ['isPartOf'=>$idwebPage, 'properties'=>'image']);
            return $dataWebPage;
        }

        // ALL and NEW
        $dataWebSite = Api::get('webSite',['id'=>$id,'properties'=>'*']);
        $data = $dataWebSite[0];
        $idwebSite = ArrayTool::searchByValue($data['identifier'],'id','value');

        // list all webpages if not isset action
        if (!$action) {
            $data['hasPart'] = Api::get('webPage', ['format'=>'ItemList','isPartOf'=>$idwebSite,'properties'=>'isPartOf,dateModified','orderBy'=>'dateModified desc']);

        } elseif ($action == 'sitemap') {
            $data['sitemaps'] = (new Sitemap())->getSitemaps();

        } elseif ($action == 'search') {
            $data['hasPart']  = Api::get('webPage', [
                'format'=>'ItemList',
                'isPartOf'=>$idwebSite,
                'properties'=>'isPartOf,dateModified',
                'nameLike' => $search,
                'orderBy'=>'dateModified desc'
            ]);
        }
        // response
        return $data;
    }
}
