<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Event;

use Plinct\Cms\Response\View\Fragment\Fragment;

abstract class EventAbstract
{
    /**
     * @var ?string
     */
    protected ?string $idevent = null;

    /**
     * @param string $case
     * @param array|null $value
     * @return array
     */
    protected function formEvent(string $case = "new", array $value = null): array
    {
      // VARS
      $startDate = isset($value) ? strstr($value['startDate'], " ", true) : null;
      $startTime = isset($value) ? substr(strstr($value['startDate'], " "), 1) : null;
      $endDate = isset($value) ? strstr($value['endDate'], " ", true) : null;
      $endTime = isset($value) ? substr(strstr($value['endDate'], " "), 1) : null;
      $description = isset($value['description']) ? stripslashes($value['description']) : null;

        // FROM
        $form = Fragment::form(["class"=>"formPadrao form-event"]);
        $form->action("/admin/event/$case")->method("post");
        $form->content("<h4>"._("Event")."</h4>");
        // HIDDENS
        if ($case == "edit") $form->input('id', (string)$this->idevent, 'hidden');
        // TITLE
        $form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
        // START DATE
        $form->fieldsetWithInput('startDate', $startDate, _("Start date"), "date");
        // START TIME
        $form->fieldsetWithInput('startTime', $startTime, _("Start time"), "time");
        // END DATE
        $form->fieldsetWithInput('endDate', $endDate, _("End date"), "date");
        // END TIME
        $form->fieldsetWithInput('endTime', $endTime, _("End time"), "time");
        // DESCRIPTION
        $form->fieldsetWithTextarea('description', $description, _('Description'), null, ['id'=>"textareaDescritionEvent$case"]);
        $form->setEditor("textareaDescritionEvent$case");
        // BUTTONS
        $form->submitButtonSend();
        if ($case == "edit") $form->submitButtonDelete("/admin/event/erase");
        // READY
        return $form->ready();
    }
}
