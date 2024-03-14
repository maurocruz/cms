<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\CreativeWork;

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

	public function __construct(?array $data = null)
	{
		if($data) {
			var_dump($data);
		}
	}

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
		$navbarCreativeWork = new CreativeWork();
		$navbarCreativeWork->navbar();
    CmsFactory::view()->addHeader(
      CmsFactory::view()->fragment()->navbar()
        ->type('webSite')
        ->title("WebSite")
        ->level(2)
        ->newTab('/admin/webSite', CmsFactory::view()->fragment()->icon()->home())
        ->newTab('/admin/webSite/new', CmsFactory::view()->fragment()->icon()->plus())
	      ->search("/admin/webSite")
        ->ready()
    );

    if ($title) CmsFactory::view()->addHeader(
      CmsFactory::view()->fragment()->navbar()
        ->title(_($title))
        ->level($level)
        ->newTab("/admin/webSite/edit/$this->idwebSite", CmsFactory::view()->fragment()->icon()->home())
        ->newTab("/admin/webPage?idwebSite=$this->idwebSite", _("List of web pages"))
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
    $name = $value['name'] ?? null;
    $description = $value['description'] ?? null;
    $copyrightHolder = $value['copyrightHolder'] ?? null;
    $author = $value['author'] ?? null;
    $url = $value['url'] ?? null;
    $case = $id ? 'edit' : 'new';

    // form
    $form = CmsFactory::view()->fragment()->form(['class'=>'formPadrao form-webSite']);
    $form->action("/admin/webSite/$case")->method('post');
    // hidden
    if ($id) $form->input('idwebSite',(string) $id,'hidden');
    // name
    $form->fieldsetWithInput('name',$name,_('Name'));
    // url
    $form->fieldsetWithInput('url',$url,'Url');
    // description
    $form->fieldsetWithTextarea('description', $description, _("Description"));
		// copyrightHolder
	  $form->fieldsetWithInput('copyrightHolder', $copyrightHolder, _('Copyright holder'));
	  // author
	  $form->fieldsetWithInput('author', $author, _('Author'));
    // submit
    $form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
    if ($id) {
        $form->submitButtonDelete('/admin/webSite/erase',['class'=>'form-submit-button form-submit-button-delete']);
    }
    // ready
    return $form->ready();
  }
}
