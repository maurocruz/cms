<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\WebSite;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Fragment\IconFragment;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Web\Element\Form;

class WebSiteAbstract
    {
    protected array $content = [];
    protected string $idwebSite;
    protected string $idwebPage;

    use FormElementsTrait;

    protected function navbarWebSite($title = null)
    {
        View::contentHeader(
            Fragment::navbar()
                ->type('webSite')
                ->title("WebSite")
                ->level(2)
                ->newTab('/admin/webSite', Fragment::icon()->home())
                ->newTab('/admin/webSite/new', Fragment::icon()->plus())
                ->search("/admin/webSite")
                ->ready()
        );

        if ($title) View::contentHeader(
            Fragment::navbar()
                ->title(_($title))
                ->level(3)
                ->newTab("/admin/webSite/edit/$this->idwebSite", Fragment::icon()->home())
                ->newTab("/admin/webSite/webPage?id=$this->idwebSite", _("List of web pages"))
                ->ready()
        );
    }

    /**
     * @return array
     */
    protected static function newView(): array
    {
        return Fragment::box()->simpleBox(self::formWebSite(), _('Add new'));
    }

    /**
     * @param $value
     * @return array
     */
    protected static function editView($value): array
    {
        return Fragment::box()->simpleBox(self::formWebSite($value), $value['name']);
    }

    /**
     * @param array|null $value
     * @return array
     */
    private static function formWebSite(array $value = null): array
    {
        //vars
        $id = $value['idwebSite'] ?? null;
        $name = $value['name'] ?? null;
        $description = $value['description'] ?? null;
        $url = $value['url'] ?? null;
        $case = $id ? 'edit' : 'new';

        // form
        $form = new Form();
        $form->action("/admin/webSite/$case")->method('post')->attributes(['class'=>'formPadrao form-webSite']);
        // hidden
        if ($id) {
            $form->input('id',$id,'hidden');
        }
        // name
        $form->fieldsetWithInput('name',$name,_('Name'));
        // url
        $form->fieldsetWithInput('url',$url,'Url');
        // description
        $form->fieldsetWithTextarea('description', $description, _("Description"));
        // submit
        $form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
        if ($id) {
            $form->submitButtonDelete('/admin/webSite/erase',['class'=>'form-submit-button form-submit-button-delete']);
        }
        // ready
        return $form->ready();
    }
}
