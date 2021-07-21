<?php
namespace Plinct\Cms\View\Types\WebPage;


use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\Form;

class WebPageWidget {
    protected static $idwebSite;
    protected static $idwebPage;

    public static function newWebPage(array $data): array {
        // VARS
        self::$idwebSite = ArrayTool::searchByValue($data['identifier'],'id','value');
        // FORM
        return self::formWebPage();
    }

    public static function editWebPage(array $value): array {
        // VARS
        self::$idwebSite = ArrayTool::searchByValue($value['isPartOf']['identifier'],'id','value');
        // FORM
        return self::formWebPage($value);
    }

    protected static function formWebPage(array $value = null): array {
        // VARS
        $name = $value['name'] ?? null;
        $url = $value['url'] ?? null;
        $description = $value['description'] ?? null;
        $alternativeHeadline = $value['alternativeHeadline'] ?? null;
        $case = $value ? 'edit' : 'new';
        // FORM
        $form = new Form(['class'=>'formPadrao form-webPage']);
        $form->action("/admin/webPage/$case")->method('post');
        // hidden
        $form->input('isPartOf',self::$idwebSite,'hidden');
        if ($case == "edit") $form->input('id', self::$idwebPage,'hidden');
        // title
        $form->fieldsetWithInput('name',$name,_('Title'));
        // url
        $form->fieldsetWithInput('url',$url,'Url');
        // description
        $form->fieldsetWithTextarea('description',$description,_('Description'));
        // alternativeHeadline
        $form->fieldsetWithInput('alternativeHeadline',$alternativeHeadline,_('Alternative headline'));
        // submit
        $form->submitButtonSend(['class'=>'form-submit-button form-submit-button-send']);
        // ready
        return $form->ready();
    }
}