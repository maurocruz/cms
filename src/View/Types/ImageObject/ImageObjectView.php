<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\ImageObject;

use Exception;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\View;
use Plinct\Tool\ArrayTool;

class ImageObjectView extends ImageObjectWidget
{
    /**
     * @param null $title
     */
    private function navBarImageObject($title = null)
    {
        View::navbar(_("Images"), [
            "/admin/imageObject" => Fragment::icon()->home(),
            "/admin/imageObject/new" => Fragment::icon()->plus()
        ], 2, ['table'=>'imageObject']);

        if ($title) {
            View::navbar($title, [], 3);
        }
    }

    /**
     * @param array $data
     */
    public function index(array $data)
    {
        $this->navBarImageObject();
        View::main(parent::keywordsList($data));
    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navBarImageObject("Add");
        View::main(self::upload($data['listLocation'] ?? null, $data['listKeywords'] ?? null));
    }

    /**
     * @throws Exception
     */
    public function edit(array $data)
    {
        if (!empty($data)) {
            $id = ArrayTool::searchByValue($data['identifier'], "id")['value'];
            $contentUrl = $data['contentUrl'];
            $this->navBarImageObject(_("Image") . ": $contentUrl");
            // edit image
            $content[] = self::divBox(_("Image"), "ImageObject", [
                self::formImageObjectEdit($data),
                self::infoIsPartOf($data)
            ]);
            // author
            $content[] = self::divBoxExpanding(_("Author"), "Person", [self::relationshipOneToOne("ImageObject", $id, "author", "Person", $data['author'])]);
            $content[] = self::arrowBack();

        } else {
            $this->navBarImageObject(_("Image not founded!"));
            $content[] = parent::noContent(_("Item not founded"));
        }

        View::main($content);
    }

    /**
     * @param $data
     */
    public function keywords($data)
    {
        $keywordName = $data['paramsUrl']['id'] ?? _("Undefined");
        $this->navBarImageObject(_("Keyword") . ": " . $keywordName);
        $content[] = parent::imagesList($data['list']);
        $content[] = self::arrowBack();

        View::main($content);
    }

    /**
     * @throws Exception
     */
    public function getForm(string $tableHasPart, int $idHasPart, $data = []): array
    {
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

    /**
     * @return array
     */
    protected function addImagesFromDatabase(): array
    {
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "tableHasPart", "type" => "hidden", "value" => $this->tableHasPart ] ];
        $content[] = [ "tag" => "input", "attributes" => [ "name" => "idHasPart", "type" => "hidden", "value" => $this->idHasPart ] ];
        $content[] = [ "tag" => "div", "attributes" => [ "class" => "imagesfromdatabase" ] ];
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/imageObject/new", "name" => "imagesFromDatabase", "class" => "formPadrao box", "method" => "post" ], "content" => $content ];
    }
}
