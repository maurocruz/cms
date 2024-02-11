<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\WebSite\Type\WebSite;

use Plinct\Cms\Controller\CmsFactory;

class WebSiteAbstract
{
  /**
   * @var array
   */
  protected array $content = [];
  /**
   * @var string
   */
  protected string $idwebSite;

  protected function navbarWebSite($title = null)
  {
    CmsFactory::webSite()->addHeader(
      CmsFactory::response()->fragment()->navbar()
        ->type('webSite')
        ->title("WebSite")
        ->level(2)
        ->newTab('/admin/webSite', CmsFactory::response()->fragment()->icon()->home())
        ->newTab('/admin/webSite/new', CmsFactory::response()->fragment()->icon()->plus())
        ->search("/admin/webSite")
        ->ready()
    );

    if ($title) CmsFactory::webSite()->addHeader(
      CmsFactory::response()->fragment()->navbar()
        ->title(_($title))
        ->level(3)
        ->newTab("/admin/webSite/edit/$this->idwebSite", CmsFactory::response()->fragment()->icon()->home())
        ->newTab("/admin/webSite/webPage?id=$this->idwebSite", _("List of web pages"))
        ->ready()
    );
  }

  /**
   * @return array
   */
  protected static function newView(): array {
    return CmsFactory::response()->fragment()->box()->simpleBox(self::formWebSite(), _('Add new'));
  }

  /**
   * @param $value
   * @return array
   */
  protected static function editView($value): array {
    return CmsFactory::response()->fragment()->box()->simpleBox(self::formWebSite($value), $value['name']);
  }

  /**
   * @param array|null $value
   * @return array
   */
  private static function formWebSite(array $value = null): array
  {
    //vars
    $id = $value['idwebSite'] ?? null;
    $name = $value['name'] ?? null;
    $description = $value['description'] ?? null;
    $url = $value['url'] ?? null;
    $case = $id ? 'edit' : 'new';

    // form
    $form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao form-webSite']);
    $form->action("/admin/webSite/$case")->method('post');
    // hidden
    if ($id) $form->input('idwebSite',$id,'hidden');
    // name
    $form->fieldsetWithInput('name',$name,_('Name'));
    // url
    $form->fieldsetWithInput('url',$url,'Url');
    // description
    $form->fieldsetWithTextarea('description', $description, _("Description"));
    // submit
    $form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
    if ($id) {
        $form->submitButtonDelete('/admin/webSite/erase',['class'=>'form-submit-button form-submit-button-delete']);
    }
    // ready
    return $form->ready();
  }
}
