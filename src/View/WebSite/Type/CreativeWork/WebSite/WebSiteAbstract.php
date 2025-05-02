<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\WebSite\WebSiteController;
use Plinct\Cms\View\WebSite\Type\CreativeWork\CreativeWork;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;

class WebSiteAbstract
{
  /**
   * @var array
   */
  protected array $content = [];
  /**
   * @var int
   */
  protected int $idwebSite;

	/**
	 * @param int $idwebSite
	 */
	public function setIdwebSite(int $idwebSite): void {
		$this->idwebSite = $idwebSite;
	}

	/**
	 * @param null $title
	 * @param int $level
	 * @return void
	 */
  public function navbarWebSite($title = null, int $level = 3): void
  {
		CreativeWork::navbar();
    CmsFactory::view()->addHeader(
      CmsFactory::view()->fragment()->navbar()
        ->type('webSite')
        ->title("WebSite")
        ->level(2)
        ->newTab('/admin/webSite', CmsFactory::view()->fragment()->icon()->home())
        ->newTab('/admin/webSite/new', CmsFactory::view()->fragment()->icon()->plus())
	      ->search()
        ->ready()
    );

    if ($title) CmsFactory::view()->addHeader(
      CmsFactory::view()->fragment()->navbar()
        ->title(_($title))
        ->level($level)
        ->newTab("/admin/webSite/edit/$this->idwebSite", CmsFactory::view()->fragment()->icon()->home())
	      ->newTab("/admin/webPage/new?idwebSite=$this->idwebSite", CmsFactory::view()->fragment()->icon()->plus())
        ->newTab("/admin/webPage?idwebSite=$this->idwebSite", _("Pages"))
        ->ready()
    );
  }

  /**
   * @return array
   */
  protected static function newView(): array {
    return CmsFactory::view()->fragment()->box()->simpleBox(self::formWebSite(), _('Add new'));
  }

  /**
   * @param $value
   * @return array
   */
  protected static function editView($value): array {
    return CmsFactory::view()->fragment()->box()->simpleBox(self::formWebSite($value), $value['name']);
  }

  /**
   * @param array|null $value
   * @return array
   */
  protected static function formWebSite(array $value = null): array
  {
    //vars
    $id = $value['idwebSite'] ?? null;
    $copyrightHolder = $value['copyrightHolder'] ?? null;
    $author = $value['author'] ?? null;
    $case = $id ? 'edit' : 'new';

    // form
    $form = CmsFactory::view()->fragment()->form("form-webSite",['class'=>'form-basic form-webSite']);
    $form->action("/admin/webSite/$case")->method('post');
    // hidden
    if ($id) $form->input('idwebSite',(string) $id,'hidden');
		// thing
	  $form = Thing::formContent($form, $value);
		// copyrightHolder
	  $form->chooseType(_( 'Copyright holder' ), 'copyrightHolder', array("Organization","Person"),$copyrightHolder);
	  // author
	  $form->relationshipOneToOne('person',_('Author'),'author',(int) $author);
    // submit
    $form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
    if ($id) {
        $form->submitButtonDelete('/admin/webSite/erase',['class'=>'form-submit-button form-submit-button-delete']);
    }
    // ready
    return $form->ready();
  }

	/**
	 * @param $type
	 * @return array
	 */
	protected function formSitemap($type): array
	{
		$sitemap = WebSiteController::getSitemap($type);
		$sitemaName = $sitemap->exist_sitemap();
		$form = CmsFactory::view()->fragment()->form("form-sitemap",['class' => 'form-basic form-sitemap']);
		$form->action("/admin/webSite/sitemap")->method('post');
		$form->input('type',$type,'hidden');
		if ($sitemaName) {
			$form->content("<p><a href='/$sitemaName' target='_blank'>".$sitemaName."</a></p>");
		}
		$form->fieldsetWithInput('loc', null, _('Location'));
		$form->content(" <button>"._("Generate sitemap")."</button>");
		return $form->ready();
	}
}
