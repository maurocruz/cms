<?php
namespace Plinct\Cms\View\WebSite\Type\Taxon;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Taxon implements TypeViewInterface
{
	/**
	 * @var ?int
	 */
	private ?int $idtaxon = null;

  /**
   * @param string|null $title
   */
  private function navbar(string $title = null): void
  {
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
	    ->type('taxon')
	    ->title(_('Taxon'))
	    ->newTab("/admin/taxon", CmsFactory::view()->fragment()->icon()->home())
	    ->newTab("/admin/taxon/new", CmsFactory::view()->fragment()->icon()->plus())
			->search()
	    ->ready()
		);
    if ($title) {
      CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()->type('taxon')->title($title)->ready());
    }
  }

  /**
   *
   * @param array|null $data
   * @param array|null $queryParams
   */
  public function index(?array $data, array $queryParams = null): void
  {
    $this->navbar();
	  CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('taxon')->setColumnsTable(['taxonRank'=>_('Taxon rank')])->ready());
  }

  /**
   * @param array|null $data
   * @param array|null $queryParams
   * @throws Exception
   */
  public function edit(?array $data, array $queryParams = null): void
  {
    if (!empty($data)) {
      $value = $data[0];
			$tb = CmsFactory::toolBox()::typeBuilder($value);
			$idthing = $tb->getIdthing();
      $idtaxon = $tb->getId();
			$this->idtaxon = $idtaxon;
      $this->navbar($value['name'] . " (" . $value['taxonRank'] . ")");
      // form taxon
      CmsFactory::view()->addMain([
				self::formTaxon('edit', $value, $data['parentTaxonList']),
	      CmsFactory::view()->fragment()->reactShell('imageObject')->setIdHasPart($idthing)->ready()
      ]);
    } else {
      $this->navbar();
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("No item found!")));
    }
  }

  /**
   * @param array|null $value
   * @param array|null $queryParams
   */
  public function new(?array $value, array $queryParams = null): void
  {
    $this->navbar();
		CmsFactory::view()->addMain(self::formTaxon());
  }

  /**
   * @param string $case
   * @param null $value
   * @param array|null $parentTaxonList
   * @return array
   */
  private function formTaxon(string $case = "new", $value = null, array $parentTaxonList = null): array
  {
    $form = CmsFactory::view()->fragment()->form("form-taxon", ['id'=>'taxonForm','class'=>'form-basic box form-taxon']);
    $form->action("/admin/taxon/$case")->method('post');
    // id
    if ($this->idtaxon) $form->input('idtaxon', $this->idtaxon, 'hidden');
		// THING
	  $form = Thing::formContent($form, $value);
    // scientificNameAuthorship
    $form->fieldsetWithInput("scientificNameAuthorship", $value['scientificNameAuthorship'] ?? null, _("Scientific name authorship") );
    // vernacularName
    $form->fieldsetWithInput("vernacularName", $value['vernacularName'] ?? null, _('Vernacular name'));
    // taxonRank
    $selectTaxonRank = isset($value['taxonRank']) ? [ $value['taxonRank'] => _($value['taxonRank']) ] : null;
    $form->fieldsetWithSelect("taxonRank", $selectTaxonRank, ["family"=>_("Family"), "genus" => _("Genus"), "species"=>_("Species")], _("Taxon rank"));
    // parent taxon
    $parentTaxonList = $parentTaxonList ?? [];
    $selectParentTaxon = isset($value['parentTaxon']) && !!$value['parentTaxon'] ? [ $value['parentTaxon'] => $parentTaxonList[$value['parentTaxon']]] : null;
    $form->fieldsetWithSelect('parentTaxon', $selectParentTaxon, $parentTaxonList, _("Parent taxon"));
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
    $form->fieldsetWithTextarea('citations',$value['citations'] ?? null, _('Citations'), null, ['id'=>"citations$this->idtaxon"]);
    // submit
    $form->submitButtonSend();
    if ($case == 'edit') $form->submitButtonDelete('/admin/taxon/erase');
    // ready
    return $form->ready();
  }
}
