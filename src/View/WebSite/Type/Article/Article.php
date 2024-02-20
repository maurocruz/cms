<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Article;

use Exception;
use Plinct\Cms\Controller\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class Article implements TypeInterface
{
	/**
	 * @param string|null $title
	 * @return void
	 */
  protected function navbarArticle(string $title = null): void
  {
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
		$apiHost = App::getApiHost();
    $this->navbarArticle();
	  CmsFactory::view()->addMain("<div class='plinct-shell' data-type='article' data-apihost='$apiHost' data-columnsTable='{\"edit\":\"Edit\",\"headline\":\"Título\",\"datePublished\":\"Publicado\",\"dateModified\":\"Modificado\"}'></div>");
  }
  /**
   * @param ?array $data
   * @throws Exception
   */
  public function edit(?array $data): void
  {
		$apiHost = App::getApiHost();
		$useToken = CmsFactory::controller()->user()->userLogged()->getToken();
    if (!empty($data)) {
      $value = $data[0];
      $this->navbarArticle($value['headline'] ?? null);
      if (empty($value)) {
        $content[] = CmsFactory::view()->fragment()->noContent();
      } else {
				$id = $value['idarticle'];
        $content[] = CmsFactory::view()->fragment()->box()->simpleBox( self::formArticle("edit", $value, $id), _("Article"));
        // author
        $content[] = CmsFactory::view()->fragment()->box()->expandingBox( _("Author"), CmsFactory::view()->fragment()->form()->relationshipOneToOne("Article", (string) $id, "author", "Person", $value['author']));
        // images
	      $content[] = "<div class='plinct-shell' data-type='imageObject' data-tablehaspart='article' data-idhaspart='$id' data-apihost='$apiHost' data-usertoken='$useToken'></div>";
      }
    } else {
      $this->navbarArticle();
      $content[] = CmsFactory::view()->fragment()->noContent(_("No articles were found!"));
    }
    CmsFactory::view()->addMain($content);
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
   * @param string $case
   * @param null $value
   * @param null $ID
   * @return array
   */
  static private function formArticle(string $case = "new", $value = null, $ID = null): array
  {
    $articleBody = isset($value['articleBody']) ? stripslashes($value['articleBody']) : null;
    $form = CmsFactory::view()->fragment()->form([ "name" => "article-form--$case", "id" => 'article-form', "class"=>"formPadrao form-article"]);
    $form->action("/admin/article/$case")->method('post');
    // id
    if ($case == "edit") $form->input('idarticle', (string) $ID, 'hidden');
    // title
    $form->fieldsetWithInput("headline", $value['headline'] ?? null, _("Title"));
    // article body
    $form->fieldsetWithTextarea("articleBody", $articleBody, _("Text"), null, ["id"=>"articleText$ID"]);
    $form->setEditor("articleText$ID", "editor$case$ID");
    // section
    $form->fieldsetWithInput("articleSection", $value['articleSection'] ?? null, _("Article sections") );
    // dates
    if ($case == "edit") {
      // date created
      $form->fieldsetWithInput("dateCreated", $value['dateCreated'] ?? null, _("Date created"), 'text', null, [ "disabled" ]);
      // date modified
      $form->fieldsetWithInput("dateModified", $value['dateModified'] ?? null, _("Date modified"),  "text", null, [ "disabled" ]);
      // date published
      $form->fieldsetWithInput("datePublished", $value['datePublished'] ?? null, _("Date published"), "text", null, [ "readonly" ]);
    }
    // published
    $form->content([ "tag" => "fieldset", "content" => [
      [ "tag" =>"legend", "content" => _("Publishied") ],
      [ "tag" => "label", "content" => [
        [ "tag" => "input", "attributes" => [ "name" => "publishied", "type" => "radio", "value" => 1, isset($value['publishied']) && $value['publishied'] == 1 ? "checked" : null ] ],
          " "._("Yes")
      ]],
      [ "tag" => "label", "content" => [
        [ "tag" => "input", "attributes" => [ "name" => "publishied", "type" => "radio", "value" => 0, isset($value['publishied']) && $value['publishied'] == 0 ? "checked" : null ] ],
          " "._("No")
      ]]
    ]]);
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
