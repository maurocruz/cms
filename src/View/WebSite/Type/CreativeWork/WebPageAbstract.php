<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;

abstract class WebPageAbstract
{
	protected ?string $idthing = null;
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
				->newTab("/admin/webPage?idwebSite=$this->idwebSite", CmsFactory::view()->fragment()->icon()->home(16,16))
				->newTab("/admin/webPage/new?idwebSite=$this->idwebSite", CmsFactory::view()->fragment()->icon()->plus(16,16))
				->search()
				->ready()
		);

		if ($title) CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->level(5)
				->title($title)
				->ready()
		);
	}

	protected function navbarWebSite(array $value = null) {
		$typeBuilder = new TypeBuilder('webSite', $value);
		$idwebSite = $typeBuilder->getId();
		$name = $typeBuilder->getValue('name');
		if ($idwebSite) {
			$this->idwebSite = $idwebSite;
			$webSite = new WebSite();
			$webSite->setIdwebSite($this->idwebSite);
			$webSite->navbarWebSite($name);
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
	  $headline = $value['headline'] ?? null;
		$alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$text = $value['text'] ?? null;
		$author = $value['author'] ?? null;
    $case = $value ? 'edit' : 'new';
    // FORM
    $form = CmsFactory::view()->fragment()->form(['class'=>'form-basic form-webPage']);
    $form->action("/admin/webPage/$case")->method('post');
    // hidden
    $form->input('isPartOf', (string) $this->idwebSite ,'hidden');
    if ($case == "edit") {
			$form->input('thing', (string) $this->idthing,'hidden');
			$form->input('idwebPage', (string) $this->idwebPage,'hidden');
    }
		// THING
	  $form = Thing::formContent($form, $value, ['alternateName', 'disambiguatingDescription']);
		// HEADLINE
	  $form->fieldsetWithInput('headline', $headline, _('Headline'));
	  // ALTERNATIVE HEADLINE
	  $form->fieldsetWithInput('alternativeHeadline', $alternativeHeadline, _('Alternative headline'));
	  // TEXT
	  $form->content(CmsFactory::view()->fragment()->box()->expandingBox(_("Content"),"<textarea name='text' class='webPage-text' id='contentTextareaWebPage'>$text</textarea>", false, 'width: 100%;'));
		$form->setEditor('contentTextareaWebPage');

		// AUTHOR
	  $form->content(CmsFactory::view()->fragment()->reactShell('person')
		  ->setAttribute('data-action','getItemType')
		  ->setAttribute('data-legend',_("Author"))
		  ->setAttribute('data-propertyName','author')
		  ->setAttribute('data-value',$author ?? '')
		  ->ready());
    // submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete('/admin/webPage/erase');
    // ready
    return $form->ready();
  }
}
