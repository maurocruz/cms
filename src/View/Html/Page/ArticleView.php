<?php
namespace Plinct\Cms\View\Html\Page;

use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class ArticleView implements ViewInterface {
    protected $content;

    use FormElementsTrait;

    protected function navbarArticle($title = null)     {
        $title1 = _("Article");
        $list = [ "/admin/article" => _("View all"), "/admin/article/new" => _("Add new") ];
        $level = 2;
        $append = navbarTrait::searchPopupList("article","headline");
        $this->content['navbar'][] = navbarTrait::navbar($title1, $list, $level, $append);
        if ($title) {
            $this->content['navbar'][] = navbarTrait::navbar($title, [], 3);
        }
    }

    public function index(array $data): array {
        $this->navbarArticle();
        $this->content['main'][] = FormElementsTrait::listAll($data, "article", _("List of articles"), [ "headline" => _("Title"), "datePublished" => _("Date published"), "dateModified" => _("Date modified") ]);
        return $this->content;
    }

    public function edit(array $data): array {
        $value = $data[0];
        $this->navbarArticle($value['headline'] ?? null);
        if (empty($value)) {
            $this->content['main'][] = self::noContent();
        } else {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];
            $this->content['main'][] = self::divBox(_("Article"), "article", [self::formArticle("edit", $value, $id)]);
            // author
            $this->content['main'][] = self::divBoxExpanding(_("Author"), "Person", [self::relationshipOneToOne("Article", $id, "author", "Person", $value['author'])]);
            // images
            $this->content['main'][] = self::divBoxExpanding(_("Images"), "imageObject", [(new ImageObjectView())->getForm("article", $id, $value['image'])]);
        }
        return $this->content;
    }

    public function new($data = null): array {
        $this->navbarArticle();
        $this->content['main'][] = self::divBox(_("Article"), "article", [ self::formArticle()]);
        return $this->content;
    }

    static private function formArticle($case = "new", $value = null, $ID = null): array {
        $content[] = $case == "edit" ? self::input("id", "hidden", $ID) : null;
        // title
        $content[] = self::fieldsetWithInput(_("Title"), "headline", $value['headline'] ?? null, ["style" => "width: calc(100% - 60px);"]);
        // position
        $content[] = self::fieldsetWithInput(_("Position"), "position", $value['position'] ?? null, [ "style" => "width: 40px;" ], "number", [ "min" => "1" ]);
        // article body
        $content[] = [ "tag" => "fieldset", "attributes" => ["style" => "width: 100%;"], "content" => [
            [ "tag" => "legend", "content" => _("Text") ],
            [ "tag" => "textarea", "attributes" => [ "id" => "article_body", "name"=>"articleBody", "style"=>"height: 200px;" ], "content" => stripslashes($value['articleBody'] ?? null) ]
        ]];
        $content[] = [ "tag" => "a", "attributes" => [ "href" => "javascript:void();", "onclick" => "expandTextarea('article_body',200);", "style" => "width: 96%; display: block;" ], "content" => sprintf(_("Expand textarea by %s px"), 200) ];
        // section
        $content[] = [ "tag" => "fieldset", "attributes" => ["style" => "width: calc(100% - 660px);"], "content" => [
            [ "tag" =>"legend", "content" => _("Article sections") ],
            [ "tag" => "input", "attributes" => [ "name" => "articleSection", "type" => "text", "value" => $value['articleSection'] ?? null ] ]
        ]];
        // dates
        if ($case == "edit") {
            // date created
            $content[] = self::fieldsetWithInput(_("Date created"), "dateCreated", $value['dateCreated'] ?? null, ["style" => "width: 148px;"], "text", [ "disabled" ]);
            // date modified
            $content[] = self::fieldsetWithInput(_("Date modified"), "dateModified", $value['dateModified'] ?? null, ["style" => "width: 148px;"], "text", [ "disabled" ]);
            // date published
            $content[] = self::fieldsetWithInput(_("Date published"), "datePublished", $value['datePublished'] ?? null, ["style" => "width: 148px;"], "text", [ "readonly" ]);

        }
        // published
        $content[] = [ "tag" => "fieldset", "attributes" => ["style" => "width: 110px;"], "content" => [
            [ "tag" =>"legend", "content" => _("Publishied") ],
            [ "tag" => "label", "content" => [
                [ "tag" => "input", "attributes" => [ "name" => "publishied", "type" => "radio", "value" => 1, isset($value['publishied']) && $value['publishied'] == 1 ? "checked" : null ] ],
                " "._("Yes")
            ]],
            [ "tag" => "label", "content" => [
                [ "tag" => "input", "attributes" => [ "name" => "publishied", "type" => "radio", "value" => 0, isset($value['publishied']) && $value['publishied'] == 0 ? "checked" : null ] ],
                " "._("No")
            ]]
        ]];
        // autor
       // $content[] = self::searchAndShow("author", "person", "name", $value['author']);
        // publisher
        //$content[] = self::searchAndShow("publisher", "organization", "name", $value['publisher']);
        // submit
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/article/erase") : null;

        return [ "tag"=>"form", "attributes"=> [ "name" => "article-form--$case", "id" => 'article-form', "class"=>"formPadrao", "method"=>"post", "action" => "/admin/article/$case" ], "content" => $content ];
    }
}
