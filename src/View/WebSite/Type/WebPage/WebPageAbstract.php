<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\WebPage;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\WebSite\WebSite;

abstract class WebPageAbstract
{
	/**
	 * @var int
	 */
	public int $id;
	/**
	 * @var int|null
	 */
  protected ?int $idwebPage = null;
	/**
	 * @var int|null
	 */
  protected ?int $idwebSite = null;

	/**
	 *
	 */
	protected function navbarWebPage(string $title = null)
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->type('webPage')
				->level(4)
				->title("WebPage")
				->newTab("/admin/webPage?idwebSite=$this->idwebSite", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/webPage/new?idwebSite=$this->idwebSite", CmsFactory::view()->fragment()->icon()->plus())
				->search("/admin/webPage/search")
				->ready()
		);

		if ($title) CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->level(5)
				->title($title)
				->ready()
		);
	}

	protected function navbarWebSite(array $value = null)
	{
		if (isset($value['idwebSite'])) {
			$this->idwebSite = (int)$value['idwebSite'] ?? null;
			$webSite = new WebSite();
			$webSite->setIdwebSite($this->idwebSite);
			$webSite->navbarWebSite($value['name']);
		}
	}

  /**
   * * * * * FORM * * * *
   *
   * @param array|null $value
   * @return array
   */
  protected function formWebPage(array $value = null): array
  {
    // VARS
    $name = $value['name'] ?? null;
    $url = $value['url'] ?? null;
    $description = $value['description'] ?? null;
    $alternativeHeadline = $value['alternativeHeadline'] ?? null;
    $case = $value ? 'edit' : 'new';
    // FORM
    $form = CmsFactory::view()->fragment()->form(['class'=>'formPadrao form-webPage']);
    $form->action("/admin/webPage/$case")->method('post');
    // hidden
    $form->input('isPartOf', (string) $this->idwebSite ,'hidden');
    if ($case == "edit") $form->input('idwebPage', (string) $this->idwebPage,'hidden');
    // title
    $form->fieldsetWithInput('name',$name,_('Title'));
    // url
    $form->fieldsetWithInput('url',$url,'Url');
    // DESCRIPTION
    $form->fieldsetWithTextarea('description', $description, _('Description'), null, ['id'=>"textarea$case$this->idwebPage"]);
    // alternativeHeadline
    $form->fieldsetWithInput('alternativeHeadline',$alternativeHeadline,_('Alternative headline'));
    // submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete('/admin/webPage/erase');
    // ready
    return $form->ready();
  }
}
