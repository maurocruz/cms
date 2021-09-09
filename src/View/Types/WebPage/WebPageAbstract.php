<?php
declare(strict_types=1);

namespace Plinct\Cms\View\Types\WebPage;

use Plinct\Cms\View\Widget\TableWidget;
use Plinct\Web\Element\Form;

class WebPageAbstract
{
    /**
     * @var string
     */
    protected static string $idwebSite;
    /**
     * @var string
     */
    protected static string $idwebPage;

    /**
     * @param array $data
     * @return array
     */
     protected static function listAllWebPages(array $data): array
     {
         $idwebSite = self::$idwebSite;

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
         $table->setData($data);
         // buttons
         $table->setButtonEdit("/admin/webSite/webPage?id=$idwebSite&item=[id]")
             ->setButtonDelete();

         return $table->ready();
     }

    /**
     * @param array|null $value
     * @return array
     */
    protected static function formWebPage(array $value = null): array
    {
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
        $form->submitButtonSend();
        if ($case == "edit") $form->submitButtonDelete('/admin/webPage/erase');
        // ready
        return $form->ready();
    }
}
