<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller;

use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class TaxonController implements ControllerInterface
{
    /**
     * @param null $params
     * @return array
     */
    public function index($params = null): array
    {
        $params2 = [ "format" => "ItemList", "properties" => "taxonRank,dateModified" , "orderBy" => "dateModified", "ordering" => "desc" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return Api::get("taxon", $params3);
    }

    /**
     * @param array $params
     * @return array
     */
    public function edit(array $params): array
    {
        $params2 = array_merge($params,[ "properties" => "*,image" ]);
        $data = Api::get("taxon", $params2);

        if (!empty($data)) {
            $taxonRank = $data[0]['taxonRank'];
            $parentTaxonType = $taxonRank == 'species' ? 'genus' : ($taxonRank == 'genus'
                ? 'family'
                : []);
            $parentTaxonList = api::get('taxon', ['taxonRank' => $parentTaxonType, 'orderBy' => 'name']);

            foreach ($parentTaxonList as $parentTaxonListValue) {
                $data['parentTaxonList'][$parentTaxonListValue['idtaxon']] = $parentTaxonListValue['name'];
            }
        }

        return $data;
    }

    /**
     * @param null $params
     * @return bool
     */
    public function new($params = null): bool {
        return true;
    }

    /**
     *
     */
    public function saveSitemap() {
        $params = [ "orderBy" => "taxonRank", "properties" => "url,dateModified,image" ];
        $data = Api::get("taxon", $params);
        // for type pages
        foreach ($data as $valueForType) {
            $id = ArrayTool::searchByValue($valueForType['identifier'],'id','value');
            $lastmod = DateTime::formatISO8601($valueForType['dateModified']);
            $dataForType[] = [
                "loc" => App::$HOST . "/t/taxon/$id",
                "lastmod" => $lastmod,
                "image" => $valueForType['image']
            ];
        }
        // for url (herbariodigital)
        foreach ($data as $valueForPage) {
            $lastmod = DateTime::formatISO8601($valueForPage['dateModified']);
            $url = $valueForPage['url'];
            $dataforPage[] = [
                "loc" => App::$HOST . $url,
                "lastmod" => $lastmod,
                "image" => $valueForPage['image']
            ];
        }
        (new Sitemap("sitemap-taxon.xml"))->saveSitemap(array_merge($dataforPage, $dataForType));
    }
}
