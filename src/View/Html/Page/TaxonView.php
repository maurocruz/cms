<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class TaxonView implements ViewInterface {
    private $content;
    
    use navbarTrait;
    use FormElementsTrait;
    
    private function navbarTaxon(string $title = null, $level = 2, array $list = []) {
        $appendNavbar = [ "tag" => "div", "attributes" => [ "class" => "navbar-search", "data-type" => "taxon", "data-searchfor" => "name,vernacularName" ] ];
        $this->content['navbar'][] = self::navbar("Taxon", [ "/admin/taxon" => "List All", "/admin/taxon/new" => _("Add new") ], 2, $appendNavbar);
        if ($title) {
            $this->content['navbar'][] = self::navbar($title, $list, $level);
        }
    }

    public function index(array $data): array {
        $this->navbarTaxon();
        $this->content['main'][] = self::listAll($data, "Taxon", "List of taxons", [ "taxonRank" => "Taxon rank", "parentTaxon" => "parent Taxon", "dateModified" => "date modified" ] );
        return $this->content;
    }
    
    public function edit(array $data): array {
        $value = $data[0];
        $this->navbarTaxon($value['name']." (".$value['taxonRank'].")", 3);
        $this->content['main'][] = self::formTaxon('edit', $value);
        // images
        $this->content['main'][] = self::divBoxExpanding("Images", "ImageObject", [ (new ImageObjectView())->getForm("taxon", ArrayTool::searchByValue($value['identifier'], 'id')['value'], $value['image'])]);
        return $this->content;
    }
    
    public function new($data = null): array {
        $this->navbarTaxon();
        $this->content['main'][] = self::formTaxon();
        return $this->content;
    }

    private static function formTaxon($case = "new", $value = null): array {
        $content[] = [ "tag" => "h3", "content" => _("Taxon") ];
        // id
        $content[] = $case == 'edit' ? self::input("id", "hidden", ArrayTool::searchByValue($value['identifier'], "id")['value']) : null;
        // name
        $content[] = self::fieldsetWithInput("Name", "name", $value['name'] ?? null, [ "style" => "width: calc(32% - 120px)" ]);
        // scientificNameAuthorship
        $content[] = self::fieldsetWithInput("Scientific name authorship", "scientificNameAuthorship", $value['scientificNameAuthorship'] ?? null, [ "style" => "width: calc(32% - 120px)" ]);
        // vernacularName
        $content[] = self::fieldsetWithInput("Vernacular name", "vernacularName", $value['vernacularName'] ?? null, [ "style" => "width: calc(32% - 160px)" ]);
        // taxonRank
        $content[] = self::fieldsetWithSelect("Taxon rank", "taxonRank", $value['taxonRank'] ?? null, [ "family" => "family", "genus" => "genus", "species" => "species"], [ "style" => "width: 160px" ], [ "style" => "width: 160px", "id" => "taxonRank" ]);
        // parentTaxon
        if ($value) {
            $parentTaxon = $value['parentTaxon'];
            $idParentTaxon = ArrayTool::searchByValue($parentTaxon['identifier'], "id")['value'];
            $content[] = self::fieldsetWithSelect("Parent taxon", "parentTaxon", [ $idParentTaxon => $parentTaxon['name'] ], [], [ "style" => "width: 160px" ], [ "style" => "width: 160px", "id" => "parentTaxon" ]);
        }
        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'] ?? null, [ "style" => "width: 50%; min-width: 300px;" ]);
        // description
        $content[] = self::fieldsetWithTextarea("Description", "description", $value['description'] ?? null, 100);
        // plantIdentificationKeys        
            // occurrence
            $plantIdentificationKeys[] = self::fieldsetWithInput("Occurrence", "occurrence", $value['occurrence'] ?? null, [ "style" => "width: 25%" ]);
            // flowering
            $plantIdentificationKeys[] = self::fieldsetWithInput("Flowering", "flowering", $value['flowering'] ?? null, [ "style" => "width: 25%" ]);
            // fructification
            $plantIdentificationKeys[] = self::fieldsetWithInput("Fructification", "fructification", $value['fructification'] ?? null, [ "style" => "width: 25%" ]);
            // height
            $plantIdentificationKeys[] = self::fieldsetWithInput("Height", "height", $value['height'] ?? null, [ "style" => "width: 25%" ]);
            // roots
            $plantIdentificationKeys[] = self::fieldsetWithInput("Roots", "roots", $value['roots'] ?? null, [ "style" => "width: 25%" ]);
            // leafs
            $plantIdentificationKeys[] = self::fieldsetWithInput("Leafs", "leafs", $value['leafs'] ?? null, [ "style" => "width: 25%" ]);
            // flowers
            $plantIdentificationKeys[] = self::fieldsetWithInput("Flowers", "flowers", $value['flowers'] ?? null, [ "style" => "width: 25%" ]);
            // fruits
            $plantIdentificationKeys[] = self::fieldsetWithInput("Fruits", "fruits", $value['fruits'] ?? null, [ "style" => "width: 25%" ]);
        // plantIdentificationKeys container
        $content[] = isset($value['taxonRank']) && $value['taxonRank'] == "species" ? [ "tag" => "div", "attributes" => [ "id" => "plantIdentificationKeys" ], "content" => $plantIdentificationKeys ] : NULL;
        // citations
        $content[] = self::fieldsetWithTextarea("Citations", "citations", $value['citations'] ?? null, 100);
        // submit
        $content[] = self::submitButtonSend();
        $content[] = $case == 'edit' ? self::submitButtonDelete("/admin/taxon/erase") : null;
        return [ "tag" => "form", "attributes" => [ "id" => "taxonForm", "class" => "formPadrao box", "action" => "/admin/taxon/$case", "method" => "post", "onsubmit" => "return CheckRequiredFieldsInForm(event, 'name,taxonRank')" ], "content" => $content ];
    }
}
