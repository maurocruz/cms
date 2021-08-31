<?php
namespace Plinct\Cms\View\Types\WebSite;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Fragment\IconFragment;
use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\View;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\TableWidget;
use Plinct\Web\Element\Form;

class WebSiteWidget {
    protected $content = [];
    protected $idwebSite;
    protected $idwebPage;

    use FormElementsTrait;

    protected function navbarWebSite($title = null)
    {
        View::navbar(_("WebSite"),[
            '/admin/webSite'=> Fragment::icon()->home(),
            '/admin/webSite/new'=> Fragment::icon()->plus()
        ], 2, ['table'=>'webSite']);

        if ($title) View::navbar(_($title), [
                "/admin/webSite/edit/$this->idwebSite"=>_("View website"),
                "/admin/webSite/webPage?id=$this->idwebSite"=>_("List of web pages"),
                "/admin/webSite/webPage?id=$this->idwebSite&action=new"=>_("Add new web page")
            ], 3);
    }

    protected function listAllWebPages(array $data): array {
        $hasPart = $data['hasPart'];
        // TABLE
        $table = new TableWidget(['class'=>'table-webPages']);
        // title
        $table->setTitle("List of web pages");
        // columns
        $table->setPropertyLabels([
            'id' => "ID",
            'name' => _("Name"),
            'url' => "Url"
        ]);
        // rows
        $table->setData($hasPart);
        // buttons
        $table->setButtonEdit("/admin/webSite/webPage?id=$this->idwebSite&item=[id]")
            ->setButtonDelete();
        return $table->ready();
    }


    protected static function newView(): array {
        return self::divBox2(_('Add new'), [ self::formWebSite() ]);
    }

    protected static function editView($value): array {
        return self::divBox2($value['name'],[ self::formWebSite($value)]);
    }

    private static function formWebSite(array $value = null): array {
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