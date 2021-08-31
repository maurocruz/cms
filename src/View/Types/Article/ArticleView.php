<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Article;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class ArticleView
{
    use FormElementsTrait;

    protected function navbarArticle(string $title = null)
    {
        View::navbar(_("Article"), [
            "/admin/article" => Fragment::icon()->home(),
            "/admin/article/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'article','searchBy'=>'headline'] );

        if ($title) {
            View::navbar($title, [], 3);
        }
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navbarArticle();
        View::main(FormElementsTrait::listAll($data, "article", _("List of articles"), [ "headline" => _("Title"), "datePublished" => _("Date published"), "dateModified" => _("Date modified") ]));
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
                $content[] = self::noContent();
            } else {
                $id = (int)ArrayTool::searchByValue($value['identifier'], "id")['value'];
                $content[] = self::divBox(_("Article"), "article", [self::formArticle("edit", $value, $id)]);
                // author
                $content[] = self::divBoxExpanding(_("Author"), "Person", [self::relationshipOneToOne("Article", $id, "author", "Person", $value['author'])]);
                // images
                $content[] = self::divBoxExpanding(_("Images"), "imageObject", [(new ImageObjectView())->getForm("article", $id, $value['image'])]);
            }
        } else {
            $this->navbarArticle();
            $content[] = self::noContent("No articles were found!",['class'=>'warning']);
        }

        View::main($content);
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navbarArticle();
        View::main(self::divBox(_("Article"), "article", [ self::formArticle()]));
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

        $content[] = $case == "edit" ? self::input("id", "hidden", (string)$ID) : null;
        // title
        $content[] = self::fieldsetWithInput(_("Title"), "headline", $value['headline'] ?? null, ["style" => "width: 100%;"]);
        // article body
        $content[] = [ "tag" => "fieldset", "attributes" => ["style" => "width: 100%;"], "content" => [
            [ "tag" => "legend", "content" => _("Text") ],
            [ "tag" => "textarea", "attributes" => [ "id" => "article_body", "name"=>"articleBody", "style"=>"height: 200px;" ], "content" => $articleBody ]
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
