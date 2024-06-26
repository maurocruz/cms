<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\WebPage;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\WebPageElement\WebPageElementView;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class WebPage extends WebPageAbstract implements TypeInterface
{

	/**
	 * @param ?array $value
	 * @return null
	 */
  public function index(?array $value) {
		parent::navbarWebSite($value);
		parent::navbarWebPage();
		return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('webPage')->setColumnsTable(['url'=>'Url'])->ready());
  }

  /**
   *
   * @param ?array $value
   */
  public function new(?array $value) {
    // NAVBAR
	  parent::navbarWebSite($value);
    parent::navbarWebPage("Add new webpage");
    // FORM
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage(), _("Add new webpage")));
  }

	/**
	 * @throws Exception
	 */
	public function edit(?array $data): bool {
		$typeBuilder = new TypeBuilder('webPage', $data);
		$webSite = $typeBuilder->getValue("isPartOf");
		$idcreativeWork = $typeBuilder->getPropertyValue('idcreativeWork');
		$typeBuilderWebSite = new TypeBuilder('webSite', $webSite);
	  $this->idwebSite = $typeBuilderWebSite->getId();
	  $this->idwebPage = $typeBuilder->getId();
		parent::navbarWebSite($webSite);
    self::navbarWebPage($data['name']);
    // FORM EDIT
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formWebPage($data), ("Edit")));
    // PROPERTIES
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPage",(string) $this->idwebPage, $data['identifier'])));
    // WEB ELEMENTS
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox(_("Web page elements"), (new WebPageElementView($idcreativeWork))->getForm((string) $this->idwebPage, $data['hasPart'])));
	  return true;
  }
  /**
   * @param $data
   */
  public function sitemap($data)
  {
    $this->idwebSite = $data['idwebSite'];
    self::navbarWebPage(_("Sitemaps"));
    // TITLE
    CmsFactory::view()->addMain("<h2>"._("Sitemaps")."</h2>");
    // INDEX
		/*$form = CmsFactory::view()->fragment()->form(['class'=>'formPadrao form-sitemaps']);
		$form->action('/admin/sitemap/new')->method('post');
		$form->fieldsetWithInput('urlForData', null, 'Url for data api');
		$form->fieldsetWithInput('loc', null, 'loc url');
		$form->fieldsetWithInput('lastmod', null, 'Lastmod property');
		$form->submitButtonSend();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->expandingBox('Adicionar novo sitemap', $form->ready()));*/
		//
    CmsFactory::view()->addMain(CmsFactory::view()->fragment()->miscellaneous()->sitemap($data['sitemaps']));
  }

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return [];
	}
}
