<?php
namespace Plinct\Cms\View\Types\WebPageElement;

use Plinct\Cms\View\Types\ImageObject\ImageObjectView;
use Plinct\Cms\View\Types\Intangible\PropertyValueView;
use Plinct\Cms\View\ViewInterface;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class WebPageElementView implements ViewInterface {
    private $content;
    protected $idwebPage;
    protected $idwebPageElement;
    
    use FormElementsTrait;
    use navbarTrait;

    private function navBarWebPageElement($title) {
        if ($title) {
            $this->content['navbar'][] = self::navbar($title);
        }
    }

    public function index(array $data): array {
        $this->navBarWebPageElement(_("Web page element"));
        return $this->content;
    }

    public function new($data = null): array {
        $content[] = [ "tag" => "h4", "content" => "Adicionar novo <span class=\"box-expanding--text\">[<a href=\"javascript: void(0)\" onclick=\"expandBox(this,'box-WebPageElement-add');\">Expandir</a>]</span>" ];
        $content[] = self::formWebPageElement();
        return [ "tag" => "div", "attributes" => [ "id" => "box-WebPageElement-add", "class" => "box box-expanding" ], "content" => $content ];
    }

    public function edit(array $data): array {
        $this->idwebPageElement = ArrayTool::searchByValue($data['identifier'], "id")['value'];
        $this->navBarWebPageElement(_("Web page element"));
        $webPageEditHref = "/admin/webPage/edit/".$data['isPartOf'];
        $this->content['main'][] = [ "tag" => "p", "content" => _("Is part of: "). '<a href="'.$webPageEditHref.'">'.$webPageEditHref.'</a>' ];
        $this->content['main'][] = self::divBox(_("Web page element"), "WebPageElement", [ self::editForms($data) ] );
        return $this->content;
    }

    public function editForms(array $value): array {
        // content
        $content[] = self::formWebPageElement("edit", $value);
        // attributes
        $content[] = self::divBoxExpanding(_("Properties"), "PropertyValue", [ (new PropertyValueView())->getForm("webPageElement", $this->idwebPageElement, $value['identifier']) ]);
        // images
        $content[] = self::divBoxExpanding(_("Images"), "ImageObject", [ (new ImageObjectView())->getForm("webPageElement", $this->idwebPageElement, $value['image']) ]);
        return $content;
    }

    public function getForm(string $idHasPart, array $value): array {
        $this->idwebPage = $idHasPart;
        // add new WebPagElement
        $content[] = self::divBoxExpanding(_("Add new"), "WebPageElement", [ self::formWebPageElement() ]);
        // WebPageElements hasPart
        if ($value) {
            foreach ($value as $valueWebPageElement) {
                $this->idwebPageElement = ArrayTool::searchByValue($valueWebPageElement['identifier'], "id")['value'];
                $name = strip_tags(str_replace("<br>"," ",$valueWebPageElement['name']));
                $content[] = self::divBoxExpanding("[" . $this->idwebPageElement . "] " . $name , "WebPageElement", [self::editForms($valueWebPageElement)]);
            }
        }
        return $content;
    }

    private function formWebPageElement($case = "new", $value = null): array {
        $id = $this->idwebPageElement ?? $this->idwebPage;
        $content[] = $case == "new" ? [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "value" => $this->idwebPage, "type" => "hidden" ] ] : null;
        $content[] = $case == "new" ? [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "value" => "webPage", "type" => "hidden" ] ] : null;
        $content[] = $case == "new" ? [ "tag" => "input", "attributes" => [ "name" => "isPartOf", "value" => $this->idwebPage, "type" => "hidden" ] ] : null;
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name" => "id", "value" => $this->idwebPageElement, "type" => "hidden" ] ] : null;
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 93%;" ], "content" => [
                [ "tag" => "legend", "content" => "Título" ],
                [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text", "value" => $value['name'] ?? null ] ]
            ]];
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 6%;" ], "content" => [
                [ "tag" => "legend", "content" => "Posição" ],
                [ "tag" => "input", "attributes" => [ "name" => "position", "type" => "text", "value" => $value['position'] ?? null ] ]
            ]];
        $content[] = [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => "Conteúdo (usar HTML)" ],
                [ "tag" => "textarea", "attributes" => [ "id" => "textareaPost-$id", "style" => "width: 100%;", "name" => "text" ], "content" => $value['text'] ?? null ]
            ]];            
        $content[] = [ "tag" => "a", "attributes" => [ "href" => "javascript:void();", "onclick" => "expandTextarea('textareaPost-$id',100);", "style" => "width: 96%; display: block;" ], "content" => "Expandir textarea em 100px" ];
        $content[] = self::submitButtonSend();
        $content[] = $case == "edit" ? self::submitButtonDelete("/admin/webPageElement/erase") : null;
        return [ "tag" => "form", "attributes" => [ "name" => "form-webPageElement--$case", "id" => "form-webPageElement-$case-$id", "action" => "/admin/webPageElement/$case", "class" => "formPadrao", "method" => "post" ], "content" => $content ];
    }
}
