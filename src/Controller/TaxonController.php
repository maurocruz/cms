<?php
namespace Plinct\Cms\Controller;

use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Type\Taxon;
use Plinct\Cms\App;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class TaxonController implements ControllerInterface
{
    public function index($params = null): array {
        $params2 = [ "format" => "ItemList", "properties" => "taxonRank,dateModified" , "orderBy" => "dateModified", "ordering" => "desc" ];
        $params3 = $params ? array_merge($params2, $params) : $params2;
        return (new Taxon())->get($params3);
    }
    
    public function edit(array $params): array {
        $params2 = array_merge($params,[ "properties" => "*,image,parentTaxon" ]);
        return (new Taxon())->get($params2);
    }
    
    public function new($params = null): bool {
        return true;
    }

    public function saveSitemap() {
        $dataSitemap = null;
        $params = [ "orderBy" => "taxonRank", "properties" => "url,dateModified,image" ];
        $data = (new Taxon())->get($params);
        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], 'id');
            $lastmod = DateTime::formatISO8601($value['dateModified']);
            $dataSitemap[] = [
                "loc" => App::$HOST . "/t/taxon/$id",
                "lastmod" => $lastmod,
                "image" => $value['image']
            ];
        }
        (new Sitemap("sitemap-taxon.xml"))->saveSitemap($dataSitemap);
    }
}
