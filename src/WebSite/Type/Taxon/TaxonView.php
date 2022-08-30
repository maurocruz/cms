<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Taxon;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;

class TaxonView
{
  /**
   * @param string|null $title
   * @param int $level
   */
  private function navbarTaxon(string $title = null, int $level = 2)
  {
    $list = [];
    CmsFactory::webSite()->navbar("Taxon", [
      "/admin/taxon" => CmsFactory::response()->fragment()->icon()->home(),
      "/admin/taxon/new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 2, ['table'=>'taxon']);

    if ($title) {
      CmsFactory::webSite()->navbar($title, $list, $level);
    }
  }

  /**
   * @param array $data
   */
  public function index(array $data)
  {
    $this->navbarTaxon();
    $listTable = CmsFactory::response()->fragment()->listTable()
      ->caption(_("List of taxons"))
      ->labels(_('Name'), _("Taxon rank"), _("Parent taxon"), _("Date modified"))
      ->rows($data['itemListElement'],['name','taxonRank','parentTaxon','dateModified'])
      ->setEditButton("/admin/taxon/edit/");

    CmsFactory::webSite()->addMain($listTable->ready());
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function edit(array $data)
  {
    if (!empty($data)) {
      $value = $data[0];
      $id = $value['idtaxon'];
      $this->navbarTaxon($value['name'] . " (" . $value['taxonRank'] . ")", 3);
      // form taxon
      CmsFactory::webSite()->addMain(self::formTaxon('edit', $value, $data['parentTaxonList']));
      // images
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("taxon", $id, $value['image'])));
    } else {
      $this->navbarTaxon();
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->noContent(_("No item found!")));
    }
  }

  /**
   */
  public function new() {
    $this->navbarTaxon();
    CmsFactory::webSite()->addMain(self::formTaxon());
  }

  /**
   * @param string $case
   * @param null $value
   * @param array|null $parentTaxonList
   * @return array
   */
  private static function formTaxon(string $case = "new", $value = null, array $parentTaxonList = null): array
  {
    $id = $value ? $value['idtaxon'] : null;

    $form = CmsFactory::response()->fragment()->form(['id'=>'taxonForm','class'=>'formPadrao box form-taxon','onsubmit'=>"return CheckRequiredFieldsInForm(event, 'name,taxonRank')"]);
    $form->action("/admin/taxon/$case")->method('post');
    // title
    $form->content("<h3>"._("Taxon")."</h3>");
    // id
    if ($id) $form->input('idtaxon', $id, 'hidden');
    // name
    $form->fieldsetWithInput('name', $value['name'] ?? null, _("Name"));
    // scientificNameAuthorship
    $form->fieldsetWithInput("scientificNameAuthorship", $value['scientificNameAuthorship'] ?? null, _("Scientific name authorship") );
    // vernacularName
    $form->fieldsetWithInput("vernacularName", $value['vernacularName'] ?? null, _('Vernacular name'));
    // taxonRank
    $selectTaxonRank = isset($value['taxonRank']) ? [ $value['taxonRank'] => _($value['taxonRank']) ] : null;
    $form->fieldsetWithSelect("taxonRank", $selectTaxonRank, ["family"=>_("Family"), "genus" => _("Genus"), "species"=>_("Species")], _("Taxon rank"));
    // parent taxon
    $parentTaxonList = $parentTaxonList ?? [];
    $selectParentTaxon = isset($value['parentTaxon']) ? [ $value['parentTaxon'] => $parentTaxonList[$value['parentTaxon']]] : null;
    $form->fieldsetWithSelect('parentTaxon', $selectParentTaxon, $parentTaxonList, _("Parent taxon"));
    // url
    $form->fieldsetWithInput('url', $value['url'] ?? null, "Url");
    // description
    $form->fieldsetWithTextarea('description',$value['description'] ?? null, _('Description'));
    // occurrence
    $form->fieldsetWithInput('occurrence', $value['occurrence'] ?? null, _("Occurrence"));
    // flowering
    $form->fieldsetWithInput('flowering', $value['flowering'] ?? null, _("Flowering"));
    // fructification
    $form->fieldsetWithInput('fructification', $value['fructification'] ?? null, _("Fructification"));
    // height
    $form->fieldsetWithInput('height', $value['height'] ?? null, _("Height"));
    // roots
    $form->fieldsetWithInput('roots', $value['roots'] ?? null, _("Roots"));
    // leafs
    $form->fieldsetWithInput('leafs', $value['leafs'] ?? null, _("Leafs"));
    // flowers
    $form->fieldsetWithInput('flowers', $value['flowers'] ?? null, _("Flowers"));
    // fruits
    $form->fieldsetWithInput('fruits', $value['fruits'] ?? null, _("Fruits"));
    // citations
    $form->fieldsetWithTextarea('citations',$value['citations'] ?? null, _('Citations'), null, ['id'=>"citations$id"]);
    // submit
    $form->submitButtonSend();
    if ($case == 'edit') $form->submitButtonDelete('/admin/taxon/erase');
    // ready
    return $form->ready();
  }
}
