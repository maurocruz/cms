<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\WebPageElement;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\View\WebSite\Type\Intangible\PropertyValueView;
use Plinct\Tool\ArrayTool;

class WebPageElementView
{
  /**
   * @var string
   */
  protected string $idwebPage;
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
      CmsFactory::webSite()->navbar($title, [], 2);
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
    $this->idwebPageElement = ArrayTool::searchByValue($data['identifier'], "id")['value'];
    $this->idwebPage = $data['isPartOf'];
    // NAVBAR
    $this->navBarWebPageElement(_("Web page element"));
    // IS PART OF
    $webPageEditHref = "/admin/webPage/edit/".$data['isPartOf'];
    CmsFactory::webSite()->addMain("<p>"._("Is part of: ")."<a href='$webPageEditHref'>$webPageEditHref</a></p>");
    // FORM
    CmsFactory::webSite()->addMain(CmsFactory::view()->fragment()->box()->simpleBox(self::editForms($data), _("Web page element")));
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
    $content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Properties"), (new PropertyValueView())->getForm("webPageElement", (string) $this->idwebPageElement, $value['identifier']));
    // IMAGES
    $content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Images"), (new ImageObjectView())->getForm("webPageElement",(int) $this->idwebPageElement, $value['image']));
		// RETURN
    return $content;
  }

  /**
   * @throws Exception
   */
  public function getForm(string $idHasPart, ?array $value): array
  {
    $this->idwebPage = $idHasPart;

    // add new WebPagElement
    $content[] = CmsFactory::view()->fragment()->box()->expandingBox(_("Add new"), self::formWebPageElement());

    // WebPageElements hasPart
    if ($value) {
      foreach ($value as $valueWebPageElement) {
				$this->idwebPageElement = $valueWebPageElement['idwebPageElement'];
				$name = $valueWebPageElement['name'];
				$text = $valueWebPageElement['text'];

        $title = $name ? strip_tags(str_replace("<br>"," ",$name))
	        : ($text ? substr(strip_tags($text),0,40).'...' : "");

        $content[] = CmsFactory::view()->fragment()->box()->expandingBox("[" . $this->idwebPageElement . "] " . $title , self::editForms($valueWebPageElement));
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
		$position = $value['position'] ?? null;

    $form = CmsFactory::view()->fragment()->form(['name'=>'form-webPageElement--$case','id'=>'form-webPageElement-$case-$id','class'=>'formPadrao form-webPageElement']);
    $form->action("/admin/webPageElement/$case")->method('post');

    // HIDDEN
    $form->input('tableHasPart','webPage','hidden')->input('idHasPart', $this->idwebPage,'hidden');
    if ($case == 'edit') $form->input('idwebPageElement', (string)$this->idwebPageElement, 'hidden');
    if($case == 'new') $form->input('isPartOf', $this->idwebPage, 'hidden');

    // NAME
    $form->fieldsetWithInput('name', $value['name'] ?? null, _('Title'));

    // POSITION
    $form->fieldsetWithInput('position', $position ? (string) $position : null, _('Position'));

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
