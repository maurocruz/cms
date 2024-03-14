<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Person;

use Exception;
use Plinct\Cms\Controller\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\ContactPoint;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;
use Plinct\Tool\ArrayTool;

class Person extends PersonAbstract implements TypeInterface
{
  /**
   * @param ?array $value
   */
  public function index(?array $value): void
  {
    $this->navbarPerson();
		CmsFactory::view()->addMain("
			<div
				class='plinct-shell'
				data-type='person'
				data-apihost='".App::getApiHost()."'
				data-userToken='".CmsFactory::controller()->user()->userLogged()->getToken()."'
				data-columnsTable='{\"edit\":\"Edit\",\"idperson\":\"ID\",\"name\":\"Nome\",\"dateModified\":\"Modificação\"}'
			></div>");
  }

  /**
   * @param array|null $value
   * @param
   */
  public function new(?array $value) {
      $this->navbarPerson();
      CmsFactory::View()->addMain(
				CmsFactory::View()->fragment()->box()->simpleBox(self::formPerson(),_("Add new"))
      );
  }

  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data) {
    if (!empty($data)) {
      $value = $data[0];
	    $typeBuilder = new TypeBuilder('person', $value);
      $this->id = $typeBuilder->getId();
			$idthing = $typeBuilder->getPropertyValue('idthing');
      // NAVBAR
      $this->navbarPersonEdit();
      // FORM
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formPerson('edit', $value), _("Edit person")));
      // CONTACT POINT
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Contact point"), (new ContactPoint())->getForm('person', $this->id, $value['contactPoint'])));
      // IMAGE
	    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf($idthing)->ready());

    } else {
      $this->navbarPerson();
      CmsFactory::view()->addMain(CmsFactory::view()->fragment()->noContent(_("Person is not exists!")));
    }
  }

  /**
   * @param $value
   */
  public function service($value)
  {
    if ($value['@type'] == 'Service') {
      $this->id = ArrayTool::searchByValue($value['provider']['identifier'], 'id', 'value');
      $this->name = $value['provider']['name'];

      // NAVBAR
      $this->navbarPersonEdit();

      $service = new ServiceView();
      $service->editWithPartOf($value);

    } else {
      $this->id = ArrayTool::searchByValue($value['identifier'], 'id', 'value');
      $this->name = $value['name'];

      // NAVBAR
      $this->navbarPersonEdit();

      $service = new ServiceView();

      if (isset($value['action']) && $value['action'] ==  "new") {
        $service->newWithPartOf($value);
      } else {
        $service->listServices($value);
      }
    }
  }

  public function product($value)
  {
    $action = $value['action'] ?? null;
    $this->name = $value['name'];
    $this->id = ArrayTool::searchByValue($value['identifier'],'id','value');

    // NAVBAR
    $this->navbarPersonEdit();

    // MAIN
    $product = new ProductView();
    if ($action == 'new') {
      $product->newWithPartOf($value);
    } else {
      $product->indexWithPartOf($value);
    }
  }

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return parent::formPerson();
	}
}
