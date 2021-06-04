<?php
namespace Plinct\Cms\View\Types\ImageObject;

use Plinct\Cms\View\ViewInterface;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class ImageObjectView extends ImageObjectWidget implements ViewInterface {
    private $content;

    use navbarTrait;

    private function navBarImageObject($titleLevel3 = null) {
        $title = _("Images");
        $list = [ "/admin/imageObject" => _("List by keywords"), "/admin/imageObject/new" => _("Add") ];
        $level = 2;
        $append = self::searchPopupList("imageObject", "keywords", "groupBy=keywords&orderBy=keywords&ordering=asc");
        $this->content['navbar'][] = self::navbar($title, $list, $level, $append);
        if ($titleLevel3) {
            $this->content['navbar'][] = self::navbar($titleLevel3, [], 3);
        }
    }

    public function index(array $data): array {
        $this->navBarImageObject();
        $this->content['main'][] = parent::keywordsList($data);
        return $this->content;
    }

    public function new($data = null): array {
        $this->navBarImageObject("Add");
        $this->content['main'][] = self::upload($data['listLocation'] ?? null, $data['listKeywords'] ?? null);
        return $this->content;
    }

    public function edit(array $data): array {
        if (!empty($data)) {
            $id = ArrayTool::searchByValue($data['identifier'], "id")['value'];
            $contentUrl = $data['contentUrl'];
            $this->navBarImageObject(_("Image") . ": $contentUrl");
            // edit image
            $this->content['main'][] = self::divBox(_("Image"), "ImageObject", [
                self::formImageObjectEdit($data),
                self::infoIsPartOf($data)
            ]);
            // author
            $this->content['main'][] = self::divBoxExpanding(_("Author"), "Person", [self::relationshipOneToOne("ImageObject", $id, "author", "Person", $data['author'])]);
            $this->content['main'][] = self::arrowBack();
        } else {
            $this->navBarImageObject(_("Image not founded!"));
            $this->content['main'][] = parent::noContent(_("Item not founded"));
        }
        return $this->content;
    }

    public function keywords($data): array {
        $this->navBarImageObject(_("Keyword") . ": " . $data['paramsUrl']['id']);
        $this->content['main'][] = parent::imagesList($data['list']);
        $this->content['main'][] = self::arrowBack();
        return $this->content;
    }
            
    public function getForm($tableHasPart, $idHasPart, $data = []): array {
        $this->tableHasPart = $tableHasPart;
        $this->idHasPart = $idHasPart;
        // form for edit
        $content[] = self::editWithPartOf($data ?? []);
        // upload
        $content[] = self::upload($tableHasPart, $idHasPart);
        // save with a database image 
        $content[] = self::addImagesFromDatabase();
        return $content;
    }

    protected function addImagesFromDatabase(): array {
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "imagesfromdatabase" ] ];
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/imageObject/new", "name" => "imagesFromDatabase", "class" => "formPadrao box", "method" => "post" ], "content" => $content ];
    }
}
