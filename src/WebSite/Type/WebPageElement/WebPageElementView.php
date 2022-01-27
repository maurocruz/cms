<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\WebPageElement;

use Exception;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;

class WebPageElementView
{
    /**
     * @var int
     */
    protected int $idwebPage;
    /**
     * @var ?int
     */
    protected ?int $idwebPageElement = null;

    /**
     * @param $title
     */
    private function navBarWebPageElement($title)
    {
        if ($title) {
            View::navbar($title, [], 2);
        }
    }

    /**
     * @return void
     */
    public function index()
    {
        $this->navBarWebPageElement(_("Web page element"));
    }

    /**
     * @return array
     */
    public function new(): array
    {
        $content[] = [ "tag" => "h4", "content" => "Adicionar novo <span class=\"box-expanding--text\">[<a href=\"javascript: void(0)\" onclick=\"expandBox(this,'box-WebPageElement-add');\">Expandir</a>]</span>" ];
        $content[] = self::formWebPageElement();
        return [ "tag" => "div", "attributes" => [ "id" => "box-WebPageElement-add", "class" => "box box-expanding" ], "content" => $content ];
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function edit(array $data)
    {
        // IDS
        $this->idwebPageElement = (int)ArrayTool::searchByValue($data['identifier'], "id")['value'];
        $this->idwebPage = (int)$data['isPartOf'];

        // NAVBAR
        $this->navBarWebPageElement(_("Web page element"));

        // IS PART OF
        $webPageEditHref = "/admin/webPage/edit/".$data['isPartOf'];
        View::main("<p>"._("Is part of: ")."<a href='$webPageEditHref'>$webPageEditHref</a></p>");

        // FORM
        View::main(Fragment::box()->simpleBox(self::editForms($data), _("Web page element")));
    }

    /**
     * @param array $value
     * @return array
     * @throws Exception
     */
    public function editForms(array $value): array
    {
        // FORM CONTENT
        $content[] = self::formWebPageElement("edit", $value);

        // ATTRIBUTES
        $content[] = Fragment::box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPageElement", $this->idwebPageElement, $value['identifier']));

        // IMAGES
        $content[] = Fragment::box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("webPageElement", $this->idwebPageElement, $value['image']));

        return $content;
    }

    /**
     * @throws Exception
     */
    public function getForm(int $idHasPart, $value): array
    {
        $this->idwebPage = $idHasPart;

        // add new WebPagElement
        $content[] = Fragment::box()->expandingBox(_("Add new"), self::formWebPageElement());

        // WebPageElements hasPart
        if ($value) {
            foreach ($value as $valueWebPageElement) {
                $this->idwebPageElement = (int)ArrayTool::searchByValue($valueWebPageElement['identifier'], "id")['value'];
                $name = $valueWebPageElement['name'] ? strip_tags(str_replace("<br>"," ",$valueWebPageElement['name'])) : null;
                $content[] = Fragment::box()->expandingBox("[" . $this->idwebPageElement . "] " . $name , self::editForms($valueWebPageElement));
            }
        }
        return $content;
    }

    /**
     * @param string $case
     * @param null $value
     * @return array
     */
    private function formWebPageElement(string $case = "new", $value = null): array
    {
        $id = $this->idwebPageElement;

        $form = Fragment::form(['name'=>'form-webPageElement--$case','id'=>'form-webPageElement-$case-$id','class'=>'formPadrao form-webPageElement']);
        $form->action("/admin/webPageElement/$case")->method('post');

        // HIDDEN
        $form->input('tableHasPart','webPage','hidden')->input('idHasPart', (string)$this->idwebPage,'hidden');
        if ($case == 'edit') $form->input('id', (string)$this->idwebPageElement, 'hidden');
        if($case == 'new') $form->input('isPartOf', (string)$this->idwebPage, 'hidden');

        // NAME
        $form->fieldsetWithInput('name', $value['name'] ?? null, _('Title'));

        // POSITION
        $form->fieldsetWithInput('position', $value['position'] ?? null, _('Position'));

        // TEXT
        $form->fieldsetWithTextarea('text', $value['text'] ?? null, _('Text'), null, ["id"=>"textareaWebPageElement$id"]);
        $form->setEditor("textareaWebPageElement$id", "editor$case$id");

        // SUBMIT BUTTONS
        $form->submitButtonSend();
        if ($case=='edit') $form->submitButtonDelete("/admin/webPageElement/erase");

        // READY
        return $form->ready();
    }
}
