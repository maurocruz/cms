<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Intangible\Service;

use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\Controller\WebSite\Type\Intangible\Offer\OfferView;
use Plinct\Tool\ArrayTool;

class ServiceView extends ServiceAbstract
{
  /**
   *
   */
  private function navbarService()
  {
    CmsFactory::webSite()->navbar(_("Services"), [
      "/admin/$this->tableHasPart?id=$this->idHasPart&action=service" => CmsFactory::response()->fragment()->icon()->home(),
      "/admin/$this->tableHasPart/service?id=$this->idHasPart&action=new" => CmsFactory::response()->fragment()->icon()->plus()
    ], 4);
  }
  /**
   * @param array $data
   */
  public function indexWithPartOf(array $data)
  {
	  $this->tableHasPart = lcfirst($data['@type']);
	  $this->idHasPart = ArrayTool::searchByValue($data['identifier'], 'id', 'value');
	  // NAVBAR
	  $this->navbarService();
		//
    if (isset($data['services']['error']) || (isset($data['services']['status']) && $data['services']['status'] == 'error')) {
      $message = $data['services']['error']['message'] ?? $data['services']['message'] ?? "error";
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->error()->installSqlTable('service', $message));
    } else {
      // VARS
      // LIST
      $listIndex = CmsFactory::response()->fragment()->listTable(['class' => 'table']);
      $listIndex->caption(sprintf(_("List of %s"), _("services")));
      $listIndex->labels(_('Name'), _("Category"), _("Date modified"));
      $listIndex->setEditButton($_SERVER['REQUEST_URI'] . "&item=");
      $listIndex->rows($data['services']['itemListElement'], ['name', 'category', 'dateModified']);
      // VIEW
      CmsFactory::webSite()->addMain($listIndex->ready());
    }
  }
  /**
   * @param null $value
   */
  public function newWithPartOf($value = null)
  {
    $this->tableHasPart = lcfirst($value['@type']);
    $this->idHasPart = ArrayTool::searchByValue($value['identifier'],'id','value');
    // NAVBAR
    $this->navbarService();
    // FORM
    CmsFactory::webSite()->addMain(parent::newWithPartOfForm());
  }
  /**
   * @param array $data
   */
  public function editWithPartOf(array $data)
  {
    $this->tableHasPart = lcfirst($data['provider']['@type']);
    $this->idHasPart = ArrayTool::searchByValue($data['provider']['identifier'],'id','value');

    // NAVBAR
    $this->navbarService();

    if (empty($data)) {
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->miscellaneous()->message());
    } else {
      // EDIT SERVICE
      CmsFactory::webSite()->addMain(self::serviceForm("edit", $data));
      // OFFER
      CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->expandingBox( _("Offer"), (new OfferView())->editWithPartOf($data) ));
    }
  }
  /**
   * @param $value
   */
  public function listServices($value)
  {
    $this->tableHasPart = lcfirst($value['@type']);
    $this->idHasPart = ArrayTool::searchByValue($value['identifier'],'id','value');
    // NAVBAR
    $this->navbarService();
    // LIST TABLE IN MAIN
    $listTable = CmsFactory::response()->fragment()->listTable();
    $listTable->setEditButton("/admin/$this->tableHasPart/service?id=$this->idHasPart&item=");
    // caption
    $listTable->caption(sprintf(_("%s services list"),$value['name']));
    // labels
    $listTable->labels('id',_('Name'),_("Date modified"));
    // rows
    $listTable->rows($value['services']['itemListElement'],['idservice','name','dateModified']);
    // VIEW
    CmsFactory::webSite()->addMain($listTable->ready());
  }
}
