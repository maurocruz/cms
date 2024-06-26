<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Thing\Thing;
use Plinct\Cms\View\WebSite\Type\TypeBuilder;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Article implements TypeInterface
{
	/**
	 * @param string|null $title
	 * @return void
	 */
  protected function navbarArticle(string $title = null): void
  {
	  CreativeWork::navbar();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar(_("Article"), [
		      "/admin/article" => CmsFactory::view()->fragment()->icon()->home(18,18),
		      "/admin/article/new" => CmsFactory::view()->fragment()->icon()->plus(18,18)
	      ], 2, ['table'=>'article','searchBy'=>'headline'] )->ready()
		);
		//
    if ($title) {
			CmsFactory::view()->addHeader(
        CmsFactory::view()->fragment()->navbar($title, [], 3)->ready()
			);
    }
  }
  /**
   *
   * @param array|null $value
   */
  public function index(?array $value): void
  {
    $this->navbarArticle();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('article')->setColumnsTable(['headline'=>_('Title'),'creativeWorkStatus'=>_("Creative work status")])->ready());
  }
	/**
	 * @param array|null $value
	 * @param
	 */
	public function new(?array $value): void
	{
		$this->navbarArticle();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::formArticle(),_("Article")));
	}
  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data): void
  {
		if (isset($data[0])) {
      $value = $data[0];
			$typeBuilder = new TypeBuilder('article', $value);
			$idarticle = $typeBuilder->getId();
			$idthing = (int) $typeBuilder->getPropertyValue('idthing');
      $this->navbarArticle($value['headline'] ?? null);
      if (empty($value)) {
        $content[] = CmsFactory::view()->fragment()->noContent();
      } else {
        $content[] = CmsFactory::view()->fragment()->box()->simpleBox(self::formArticle("edit", $value, $idarticle), _("Article"));
        // images
	      $content[] = CmsFactory::view()->fragment()->reactShell('imageObject')->setIsPartOf($idthing)->ready();
      }
    } else {
      $this->navbarArticle();
      $content[] = CmsFactory::view()->fragment()->noContent(_("No articles were found!"));
    }
    CmsFactory::view()->addMain($content);
  }
  /**
   * @param string $case
   * @param null $value
   * @param null $ID
   * @return array
   */
  static private function formArticle(string $case = "new", $value = null, $ID = null): array
  {
	  $headline = $value['headline'] ?? null;
	  $alternativeHeadline = $value['alternativeHeadline'] ?? null;
		$about = $value['about'] ?? null;
    $articleBody = isset($value['articleBody']) ? stripslashes($value['articleBody']) : null;
		$author = $value['author'] ?? null;
		$creativeWorkStatus = $value['creativeWorkStatus'] ?? null;
		// FORM
    $form = CmsFactory::view()->fragment()->form(["class"=>"form-basic form-article"]);
    $form->action("/admin/article/$case")->method('post');
    // id
    if ($case == "edit") $form->input('idarticle', (string) $ID, 'hidden');
		// THING
		$form = Thing::formContent($form, $value, ['alternateName', 'disambiguatingDescription']);
	  // about
	  $form->relationshipOneToOne('thing',_('About'),'about',$about);
    // HEADLINE
    $form->fieldsetWithInput("headline", $headline, _("Title"));
	  // ALTERNATIVE HEADLINE
	  $form->fieldsetWithInput('alternativeHeadline', $alternativeHeadline, _('Alternative headline'));
    // article body
	  $form->content(CmsFactory::view()->fragment()->box()->expandingBox(
			_('Article body'),
			"<textarea name='articleBody' class='article-articleBody' id='articleBody$ID'>$articleBody</textarea>", false, 'width: 100%;'));
    $form->setEditor("articleBody$ID", "editor$case$ID");

    // section
    $form->fieldsetWithInput("articleSection", $value['articleSection'] ?? null, _("Article sections") );
		// author
	  $form->relationshipOneToOne('person',_("Author"),'author',(int) $author);
	  // creative work status
		$form->fieldsetWithSelect('creativeWorkStatus', $creativeWorkStatus,[
			"draft"=>_("Draft"),
			"in production"=>_("In production"),
			"suspended"=>_("Suspended"),
			"Waiting for review"=>_("Waiting for review"),
			"published"=>_("Published")
		],_("Creative work status"), ['class'=>'form-article-creativeWorkStatus']);
    // dates
    if ($case == "edit" && is_array($value)) {
	    $typeBuilder = new TypeBuilder('article', $value);
	    $dateCreated = $typeBuilder->getPropertyValue('dateCreated');
	    $dateModified = $typeBuilder->getPropertyValue('dateModified');
      // date created
      $form->fieldsetWithInput("dateCreated", $dateCreated, _("Date created"), 'datetime-local', ['class'=>'form-article-dateCreated' ], ['disabled']);
      // date modified
      $form->fieldsetWithInput("dateModified", $dateModified, _("Date modified"),  "datetime-local", ['class'=>'form-article-dateModified'], ['disabled']);
      // date published
      $form->fieldsetWithInput("datePublished", $value['datePublished'] ?? null, _("Date published"), "datetime-local", ['class'=>'form-article-datePublished'], ['readonly']);
    }

    // submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/article/erase");
    return $form->ready();
  }
}
