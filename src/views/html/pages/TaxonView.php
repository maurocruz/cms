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
        
        $content['main'][] = self::listAll($data, "Taxon", "List of taxons" );
        
        return $content;
    }
    
    public function edit(array $data): array 
    {        
        $value = $data[0];
        
        $this->navbarTaxon($value['name'], 3);
                
        $this->content['main'][] = self::form('edit', $value);
        
        // images
        $this->content['main'][] = self::divBoxExpanding("Images", "ImageObject", [ (new ImageObjectView())->getForm("Taxon", PropertyValue::extractValue($value['identifier'], 'id'), $value['image'])]);
        
        return $this->content;
    }
    
    private static function form($case = "add", $value)
    {        
        //var_dump($value);
        
        $content[] = [ "tag" => "h3", "content" => _("Taxon") ];
                
        // id
        $content[] = $case == 'edit' ? self::input("id", "hidden", PropertyValue::extractValue($value['identifier'], 'id')) : null;
        
        // name
        $content[] = self::fieldsetWithInput("Name", "name", $value['name']);
        
        // taxonRank
        $content[] = self::fieldsetWithInput("Taxon rank", "taxonRank", $value['taxonRank']);
        
        // parentTaxon
        $content[] = self::fieldsetWithInput("Parent taxon", "parentTaxon", $value['parentTaxon']);
        
        // vernacularName
        $content[] = self::fieldsetWithInput("Vernacular name", "vernacularName", $value['vernacularName']);
        
        // description
        $content[] = self::fieldsetWithTextarea("Description", "description", $value['description']);
        
        // uso
        $content[] = self::fieldsetWithTextarea("Uso", "uso", $value['uso']);
        
        // obs
        $content[] = self::fieldsetWithTextarea("Obs", "obs", $value['obs']);
        
        // fontes
        $content[] = self::fieldsetWithTextarea("Fontes", "fontes", $value['fontes']);
        
        // submit
        $content[] = self::submitButtonSend();
        
        $content[] = $case == 'edit' ? self::submitButtonDelete("/admin/taxon/erase") : null;
        
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao box" ], "content" => $content ];
    }
}
