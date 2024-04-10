<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Article;

use Exception;
use Plinct\Cms\Controller\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\CreativeWork;
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
		      "/admin/article" => CmsFactory::view()->fragment()->icon()->home(),
		      "/admin/article/new" => CmsFactory::view()->fragment()->icon()->plus()
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
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('article')->ready()
		);
  }
	/**
	 * @param array|null $value
	 * @param
	 */
	public function new(?array $value) {
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
        // author
        //$content[] = CmsFactory::view()->fragment()->box()->expandingBox( _("Author"), CmsFactory::view()->fragment()->form()->relationshipOneToOne("Article", (string) $idarticle, "author", "Person", $value['author']));
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
		$typeBuilder = new TypeBuilder('article', $value);
		$dateCreated = $typeBuilder->getPropertyValue('dateCreated');
		$dateModified = $typeBuilder->getPropertyValue('dateModified');
    $articleBody = isset($value['articleBody']) ? stripslashes($value['articleBody']) : null;
		$author = $value['author'] ?? null;
		$creativeWorkStatus = $value['creativeWorkStatus'] ?? null;
		// FORM
    $form = CmsFactory::view()->fragment()->form(["class"=>"form-basic form-article"]);
    $form->action("/admin/article/$case")->method('post');
    // id
    if ($case == "edit") $form->input('idarticle', (string) $ID, 'hidden');
		$form = Thing::formContent($form, $value);
    // title
    $form->fieldsetWithInput("headline", $value['headline'] ?? null, _("Title"));
    // article body
    $form->fieldsetWithTextarea("articleBody", $articleBody, _("Text"), null, ["id"=>"articleText$ID"]);
    $form->setEditor("articleText$ID", "editor$case$ID");
    // section
    $form->fieldsetWithInput("articleSection", $value['articleSection'] ?? null, _("Article sections") );
		// author
	  $form->fieldsetWithInput('author', $author, _("Author"));
	  // creative work status
	  $form->fieldsetWithInput('creativeWorkStatus', $creativeWorkStatus, _("Creative Work status"));
    // dates
    if ($case == "edit") {
      // date created
      $form->fieldsetWithInput("dateCreated", $dateCreated, _("Date created"), 'text', null, [ "disabled" ]);
      // date modified
      $form->fieldsetWithInput("dateModified", $dateModified, _("Date modified"),  "text", null, [ "disabled" ]);
      // date published
      $form->fieldsetWithInput("datePublished", $value['datePublished'] ?? null, _("Date published"), "text", null, [ "readonly" ]);
    }

    // submit
    $form->submitButtonSend();
    if ($case == "edit") $form->submitButtonDelete("/admin/article/erase");
    return $form->ready();
  }

	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return [];
	}
}
