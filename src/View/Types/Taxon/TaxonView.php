<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Taxon;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class TaxonView
{
    use FormElementsTrait;

    /**
     * @param string|null $title
     * @param int $level
     * @param array $list
     */
    private function navbarTaxon(string $title = null, int $level = 2, array $list = [])
    {
        View::navbar("Taxon", [
            "/admin/taxon" => Fragment::icon()->home(),
            "/admin/taxon/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'taxon']);

        if ($title) {
            View::navbar($title, $list, $level);
        }
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navbarTaxon();
        View::main(self::listAll($data, "Taxon", "List of taxons", [ "taxonRank" => "Taxon rank", "parentTaxon" => "parent Taxon", "dateModified" => "date modified" ]));
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function edit(array $data)
    {
        $value = $data[0];
        $id = (int)ArrayTool::searchByValue($value['identifier'], 'id', 'value');

        $this->navbarTaxon($value['name']." (".$value['taxonRank'].")", 3);

        // form taxon
        View::main(self::formTaxon('edit', $value));

        // images
        View::main(self::divBoxExpanding("Images", "ImageObject", [ (new ImageObjectView())->getForm("taxon", $id, $value['image'])]));
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navbarTaxon();
        View::main(self::formTaxon());
    }

    /**
     * @param string $case
     * @param null $value
     * @return array
     */
    private static function formTaxon(string $case = "new", $value = null): array
    {
        $id = $value ? ArrayTool::searchByValue($value['identifier'], 'id','value') : null;

        // TODO atualizar formulário taxon
        /*$form = new Form(['id'=>'taxonForm','class'=>'formPadrao box form-taxon','onsubmit'=>"return CheckRequiredFieldsInForm(event, 'name,taxonRank')"]);
        $form->action("/admin/taxon/$case")->method('post');
        // title
        $form->content("<h3>"._("Taxon")."</h3>");
        // id
        if ($id) $form->input('id', $id, 'hidden');
        // name
        $form->fieldsetWithInput('name', $value['name'] ?? null, _("Name"));

        return $form->ready();
        */
        $content[] = [ "tag" => "h3", "content" => _("Taxon") ];
        // id
        $content[] = $case == 'edit' ? self::input("id", "hidden", $id) : null;
        // name
        $content[] = self::fieldsetWithInput("Name", "name", $value['name'] ?? null);
        // scientificNameAuthorship
        $content[] = self::fieldsetWithInput("Scientific name authorship", "scientificNameAuthorship", $value['scientificNameAuthorship'] ?? null);
        // vernacularName
        $content[] = self::fieldsetWithInput("Vernacular name", "vernacularName", $value['vernacularName'] ?? null);
        // taxonRank
        $content[] = self::fieldsetWithSelect("Taxon rank", "taxonRank", $value['taxonRank'] ?? null, [ "family" => "family", "genus" => "genus", "species" => "species"], null, [ "id" => "taxonRank" ]);
        // parentTaxon
        if ($value) {
            $parentTaxon = $value['parentTaxon'];
            $idParentTaxon = ArrayTool::searchByValue($parentTaxon['identifier'], "id")['value'];
            $content[] = self::fieldsetWithSelect("Parent taxon", "parentTaxon", [ $idParentTaxon => $parentTaxon['name'] ], [], null, ["id" => "parentTaxon" ]);
        }
        // url
        $content[] = self::fieldsetWithInput("Url", "url", $value['url'] ?? null);
        // description
        $content[] = self::fieldsetWithTextarea("Description", "description", $value['description'] ?? null, 100);
        // occurrence
        $content[] = self::fieldsetWithInput("Occurrence", "occurrence", $value['occurrence'] ?? null);
        // flowering
        $content[] = self::fieldsetWithInput("Flowering", "flowering", $value['flowering'] ?? null);
        // fructification
        $content[] = self::fieldsetWithInput("Fructification", "fructification", $value['fructification'] ?? null);
        // height
        $content[] = self::fieldsetWithInput("Height", "height", $value['height'] ?? null);
        // roots
        $content[] = self::fieldsetWithInput("Roots", "roots", $value['roots'] ?? null);
        // leafs
        $content[] = self::fieldsetWithInput("Leafs", "leafs", $value['leafs'] ?? null);
        // flowers
        $content[] = self::fieldsetWithInput("Flowers", "flowers", $value['flowers'] ?? null);
        // fruits
        $content[] = self::fieldsetWithInput("Fruits", "fruits", $value['fruits'] ?? null);
        // citations
        $content[] = self::fieldsetWithTextarea("Citations", "citations", $value['citations'] ?? null, 100);
        // submit
        $content[] = self::submitButtonSend();
        $content[] = $case == 'edit' ? self::submitButtonDelete("/admin/taxon/erase") : null;
        return [ "tag" => "form", "attributes" => [ "id" => "taxonForm", "class" => "formPadrao box", "action" => "/admin/taxon/$case", "method" => "post", "onsubmit" => "return CheckRequiredFieldsInForm(event, 'name,taxonRank')" ], "content" => $content ];
    }
}
