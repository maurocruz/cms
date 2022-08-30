<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Article;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Tool\ArrayTool;

class ArticleView
{
  protected function navbarArticle(string $title = null)
  {
		CmsFactory::webSite()->navbar(
			_("Article"),
			[
	      "/admin/article" => CmsFactory::response()->fragment()->icon()->home(),
	      "/admin/article/new" => CmsFactory::response()->fragment()->icon()->plus()
      ], 2, ['table'=>'article','searchBy'=>'headline'] );

    if ($title) {
        CmsFactory::webSite()->navbar($title, [], 3);
    }
  }

  /**
   * @param array $data
   */
  public function index(array $data)
  {
    $this->navbarArticle();

    $listTable = CmsFactory::response()->fragment()->listTable();
    $listTable->caption(_("List of articles"))
      ->labels(_('Title'), _("Date modified"),_("Date published"))
      ->rows($data['itemListElement'],['headline','dateModified','datePublished'])
      ->setEditButton("/admin/article/edit/");

    CmsFactory::webSite()->addMain($listTable->ready());
  }

  /**
   * @param array $data
   * @throws Exception
   */
  public function edit(array $data)
  {
    if (!empty($data)) {
      $value = $data[0];
      $this->navbarArticle($value['headline'] ?? null);
      if (empty($value)) {
        $content[] = CmsFactory::response()->fragment()->noContent();
      } else {
        $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
        $content[] = CmsFactory::response()->fragment()->box()->simpleBox( self::formArticle("edit", $value, $id), _("Article"));
        // author
        $content[] = CmsFactory::response()->fragment()->box()->expandingBox( _("Author"), CmsFactory::response()->fragment()->form()->relationshipOneToOne("Article", (string) $id, "author", "Person", $value['author']));
        // images
        $content[] = CmsFactory::response()->fragment()->box()->expandingBox( _("Images"), (new ImageObjectView())->getForm("article", $id, $value['image']));
      }
    } else {
      $this->navbarArticle();
      $content[] = CmsFactory::response()->fragment()->noContent(_("No articles were found!"));
    }

    CmsFactory::webSite()->addMain($content);
  }

  /**
   * @param
   */
  public function new() {
    $this->navbarArticle();
    CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->box()->simpleBox(self::formArticle(),_("Article")));
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

    $form = CmsFactory::response()->fragment()->form([ "name" => "article-form--$case", "id" => 'article-form', "class"=>"formPadrao form-article"]);
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
}
