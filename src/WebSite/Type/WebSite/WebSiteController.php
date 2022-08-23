<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebSite;

use Plinct\Cms\CmsFactory;
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
			return CmsFactory::server()->api()->get('webSite',['format'=>'ItemList','properties'=>'name,url'])->ready();
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $id = $params['id'] ?? $params['idwebSite'] ?? null;
        $data = CmsFactory::server()->api()->get('webSite',['id'=>$id, 'properties'=>'hasPart'])->ready();
        if (isset($data[0]['identifier'])) {
            $idSite = ArrayTool::searchByValue($data[0]['identifier'], 'id', 'value');
            $data[0]['hasPart'] = CmsFactory::server()->api()->get('webPage', ['isPartOf' => $idSite, 'orderBy' => 'dateModified desc'])->ready();
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
            $dataWebPage = CmsFactory::server()->api()->get('webPage',['id'=>$item,'properties'=>'*,isPartOf'])->ready()[0];

            $idwebPage = $dataWebPage['idwebPage'];
            $dataWebPage['hasPart'] = CmsFactory::server()->api()->get('webPageElement', ['isPartOf'=>$idwebPage, 'properties'=>'image'])->ready();
            return $dataWebPage;
        }

        // ALL and NEW
        $dataWebSite = CmsFactory::server()->api()->get('webSite',['id'=>$id,'properties'=>'*'])->ready();
        $data = $dataWebSite[0];
        $idwebSite = ArrayTool::searchByValue($data['identifier'],'id','value');

        // list all webpages if not isset action
        if (!$action) {
            $data['hasPart'] = CmsFactory::server()->api()->get('webPage', ['format'=>'ItemList','isPartOf'=>$idwebSite,'properties'=>'isPartOf,dateModified','orderBy'=>'dateModified desc'])->ready();

        } elseif ($action == 'sitemap') {
            $data['sitemaps'] = (new Sitemap())->getSitemaps();

        } elseif ($action == 'search') {
            $data['hasPart']  = CmsFactory::server()->api()->get('webPage', [
                'format'=>'ItemList',
                'isPartOf'=>$idwebSite,
                'properties'=>'isPartOf,dateModified',
                'nameLike' => $search,
                'orderBy'=>'dateModified desc'
            ])->ready();
        }
        // response
        return $data;
    }
}
