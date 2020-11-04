<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Cms\View\Html\Widget\FormElementsTrait;
use Plinct\Cms\View\Html\Widget\navbarTrait;

class TaxonView implements ViewInterface
{
    private $content;
    
    use navbarTrait;
    use FormElementsTrait;
    
    private function navbarTaxon(string $title = null, $level = 2, array $list = []) 
    {
        $appendNavbar = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "taxon", "data-searchfor" => "name,vernacularName" ] ];

        $this->content['navbar'][] = self::navbar("Taxon", [ "/admin/taxon" => "List All", "/admin/taxon/new" => _("Add new") ], 2, $appendNavbar);
        
        if ($title) {
            $this->content['navbar'][] = self::navbar($title, $list, $level);
        }
    }

    public function index(array $data): array
    {
        $this->navbarTaxon();
        
        $this->content['main'][] = self::listAll($data, "Taxon", "List of taxons", [ "taxonRank" => "Taxon rank", "parentTaxon" => "parent Taxon", "dateModified" => "date modified" ] );
        
        return $this->content;
    }
    
    public function edit(array $data): array 
    {        
        $value = $data[0];
        
        $this->navbarTaxon($value['name']." (".$value['taxonRank'].")", 3);
                
        $this->content['main'][] = self::form('edit', $value);
        
        // images
        $this->content['main'][] = self::divBoxExpanding("Images", "ImageObject", [ (new ImageObjectView())->getForm("taxon", PropertyValue::extractValue($value['identifier'], 'id'), $value['image'])]);
        
        return $this->content;
    }
    
    public function new($data = null): array 
    {
        $this->navbarTaxon();
        
        $this->content['main'][] = self::form();
        
        return $this->content;
    }

    private static function form($case = "new", $value = null)
    {                
        $content[] = [ "tag" => "h3", "content" => _("Taxon") ];
                
        // id
        $content[] = $case == 'edit' ? self::input("id", "hidden", PropertyValue::extractValue($value['identifier'], 'id')) : null;
        
        // name
        $content[] = self::fieldsetWithInput("Name", "name", $value['name'], [ "style" => "width: calc(32% - 120px)" ]);
        
        // scientificNameAuthorship
        $content[] = self::fieldsetWithInput("Scientific name authorship", "scientificNameAuthorship", $value['scientificNameAuthorship'], [ "style" => "width: calc(32% - 120px)" ]);
        
        // vernacularName
        $content[] = self::fieldsetWithInput("Vernacular name", "vernacularName", $value['vernacularName'], [ "style" => "width: calc(32% - 160px)" ]);
        
        // taxonRank
        $content[] = self::fieldsetWithSelect("Taxon rank", "taxonRank", $value['taxonRank'], [ "family" => "family", "genus" => "genus", "species" => "species"], [ "style" => "width: 160px" ], [ "style" => "width: 160px", "id" => "taxonRank" ]);
        
        // parentTaxon
        $parentTaxon = $value['parentTaxon'];
        $idParentTaxon = PropertyValue::extractValue($parentTaxon['identifier'], "id");
        $content[] = self::fieldsetWithSelect("Parent taxon", "parentTaxon", [ $idParentTaxon => $parentTaxon['name'] ], [], [ "style" => "width: 160px" ], [ "style" => "width: 160px", "id" => "parentTaxon" ]);

        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'], [ "style" => "width: 50%; min-width: 300px;" ]);

        // description
        $content[] = self::fieldsetWithTextarea("Description", "description", $value['description'], 100);
        
        // plantIdentificationKeys        
            // occurrence
            $plantIdentificationKeys[] = self::fieldsetWithInput("Occurrence", "occurrence", $value['occurrence'], [ "style" => "width: 25%" ]);

            // flowering
            $plantIdentificationKeys[] = self::fieldsetWithInput("Flowering", "flowering", $value['flowering'], [ "style" => "width: 25%" ]);

            // fructification
            $plantIdentificationKeys[] = self::fieldsetWithInput("Fructification", "fructification", $value['fructification'], [ "style" => "width: 25%" ]);

            // height
            $plantIdentificationKeys[] = self::fieldsetWithInput("Height", "height", $value['height'], [ "style" => "width: 25%" ]);

            // roots
            $plantIdentificationKeys[] = self::fieldsetWithInput("Roots", "roots", $value['roots'], [ "style" => "width: 25%" ]);

            // leafs
            $plantIdentificationKeys[] = self::fieldsetWithInput("Leafs", "leafs", $value['leafs'], [ "style" => "width: 25%" ]);

            // flowers
            $plantIdentificationKeys[] = self::fieldsetWithInput("Flowers", "flowers", $value['flowers'], [ "style" => "width: 25%" ]);

            // fruits
            $plantIdentificationKeys[] = self::fieldsetWithInput("Fruits", "fruits", $value['fruits'], [ "style" => "width: 25%" ]);
        
        // plantIdentificationKeys container
        $content[] = $value['taxonRank'] == "species" ? [ "tag" => "div", "attributes" => [ "id" => "plantIdentificationKeys" ], "content" => $plantIdentificationKeys ] : NULL;
        
        // citations
        $content[] = self::fieldsetWithTextarea("Citations", "citations", $value['citations'], 100);
        
        // submit
        $content[] = self::submitButtonSend();
        
        $content[] = $case == 'edit' ? self::submitButtonDelete("/admin/taxon/erase") : null;
        
        return [ "tag" => "form", "attributes" => [ "id" => "taxonForm", "class" => "formPadrao box", "action" => "/admin/taxon/$case", "method" => "post", "onsubmit" => "return CheckRequiredFieldsInForm(event, 'name,taxonRank')" ], "content" => $content ];
    }
}
