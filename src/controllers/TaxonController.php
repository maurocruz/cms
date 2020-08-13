<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Type\Taxon;
use Plinct\Tool\Sitemap;

class TaxonController implements ControllerInterface
{
    public function index($params = null): array 
    {        
        $params2 = [ "format" => "ItemList", "properties" => "taxonRank,dateModified" , "orderBy" => "dateModified", "ordering" => "desc" ];
        
        $params3 = $params ? array_merge($params2, $params) : $params2;
        
        return (new Taxon())->get($params3);
    }
    
    public function edit(array $params): array 
    {        
        $params2 = array_merge($params,[ "properties" => "*,image,parentTaxon" ]);
        
        return (new Taxon())->get($params2);
    }
    
    public function new() 
    {
        return true;
    }

    public function saveSitemap()
    {
        $dataSitemap = null;

        $params = [ "orderBy" => "taxonRank"];
        $data = (new Taxon())->get($params);

        foreach ($data as $value) {
            $id = PropertyValue::extractValue($value['identifier'], 'id');

            $url = $value['url'] == '' ? "/taxon?id=$id" : $value['url'];

            $dataSitemap[] = [
                "loc" => "//" . filter_input(INPUT_SERVER, 'HTTP_HOST') . $url,
                "lastmod" => str_replace(" ", "T", $value['dateModified']) . "Z"
            ];
        }

        (new Sitemap("sitemap-taxon.xml"))->createSitemap($dataSitemap);
    }
}
