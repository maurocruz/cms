<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;

class TaxonView implements ViewInterface
{
    private $content;
    
    use \Plinct\Cms\View\Html\Piece\navbarTrait;
    use \Plinct\Web\Widget\FormTrait;
    
    private function navbarTaxon(string $title = null, $level = 2, array $list = []) 
    {
        $this->content['navbar'][] = self::navbar("Taxon", [ "/admin/taxon" => "List All", "/admin/taxon/new" => _("Add new") ], 2);
        
        if ($title) {
            $this->content['navbar'][] = self::navbar($title, $list, $level);
        }
    }

    public function index(array $data): array
    {
        $this->navbarTaxon();
        
        $this->content['main'][] = self::listAll($data, "Taxon", "List of taxons", [ "taxonRank" => "Taxon rank", "dateModified" => "date modified" ] );
        
        return $this->content;
    }
    
    public function edit(array $data): array 
    {        
        $value = $data[0];
        
        $this->navbarTaxon($value['name'], 3);
                
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
        //var_dump($value);
        
        $content[] = [ "tag" => "h3", "content" => _("Taxonony") ];
                
        // id
        $content[] = $case == 'edit' ? self::input("id", "hidden", PropertyValue::extractValue($value['identifier'], 'id')) : null;
        
        // name
        $content[] = self::fieldsetWithInput("Name", "name", $value['name'], [ "style" => "width: calc(36% - 120px)" ]);
        
        // scientificNameAuthorship
        $content[] = self::fieldsetWithInput("Scientific name authorship", "scientificNameAuthorship", $value['scientificNameAuthorship'], [ "style" => "width: calc(36% - 120px)" ]);
        
        // vernacularName
        $content[] = self::fieldsetWithInput("Vernacular name", "vernacularName", $value['vernacularName'], [ "style" => "width: calc(36% - 160px)" ]);
        
        // taxonRank
        $content[] = self::fieldsetWithSelect("Taxon rank", "taxonRank", $value['taxonRank'], [ "family" => "Family", "genus" => "Genus", "species" => "Species"], [ "style" => "width: 160px" ], [ "style" => "width: 160px" ]);
        
        // parentTaxon
        $content[] = self::fieldsetWithInput("Parent taxon", "parentTaxon", $value['parentTaxon'], [ "style" => "width: 120px" ]);
        
        // family
        $content[] = self::fieldsetWithInput("Family", "family", $value['family'], [ "style" => "width: 120px" ]);
        
        // genus
        $content[] = self::fieldsetWithInput("Genus", "genus", $value['genus'], [ "style" => "width: 120px" ]);
        
        // specie
        $content[] = self::fieldsetWithInput("Specie", "specie", $value['specie'], [ "style" => "width: 120px" ]);
        
        // description
        $content[] = self::fieldsetWithTextarea("Description", "description", $value['description'], 100);
                
        // occurrence
        $content[] = self::fieldsetWithInput("Occurrence", "occurrence", $value['occurrence'], [ "style" => "width: 300px" ]);
        
        // flowering
        $content[] = self::fieldsetWithInput("Flowering", "flowering", $value['flowering'], [ "style" => "width: 300px" ]);
        
        // fructification
        $content[] = self::fieldsetWithInput("Fructification", "fructification", $value['fructification'], [ "style" => "width: 300px" ]);
        
        // height
        $content[] = self::fieldsetWithInput("Height", "height", $value['height'], [ "style" => "width: 300px" ]);
        
        // roots
        $content[] = self::fieldsetWithInput("Roots", "roots", $value['roots'], [ "style" => "width: 300px" ]);
        
        // leafs
        $content[] = self::fieldsetWithInput("Leafs", "leafs", $value['leafs'], [ "style" => "width: 300px" ]);
        
        // flowers
        $content[] = self::fieldsetWithInput("Flowers", "flowers", $value['flowers'], [ "style" => "width: 300px" ]);
        
        // fruits
        $content[] = self::fieldsetWithInput("Fruits", "fruits", $value['fruits'], [ "style" => "width: 300px" ]);
        
        // citations
        $content[] = self::fieldsetWithTextarea("Citations", "citations", $value['citations'], 100);
        
        // submit
        $content[] = self::submitButtonSend();
        
        $content[] = $case == 'edit' ? self::submitButtonDelete("/admin/taxon/erase") : null;
        
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao box", "action" => "/admin/taxon/$case", "method" => "post", "onsubmit" => "return CheckRequiredFieldsInForm(event, 'name,taxonRank')" ], "content" => $content ];
    }
}
