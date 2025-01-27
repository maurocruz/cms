<?php
namespace Plinct\Cms\View\WebSite\Type\Action;

use Plinct\Tool\DateTime;
use Plinct\Web\Element\Table;

class ActionView
{
	/**
	 * @param $data
	 * @return array
	 */
  public function view($data): array
  {
    // TABLE
    $table = new Table();
    // HEADERS
    $table->head(_("Date"), [ "style" => "width: 160px;" ])
      ->head(_("Action"), [ "style" => "width: 80px;" ])
      ->head(_("Summary"))
	    ->head(_('Status'), [ "style" => "width: 80px;" ])
      ->head(_("Author"), [ "style" => "width: 150px;" ]);
    // BODY
    if($data) {
      foreach ($data as $value) {
        $table->bodyCell(DateTime::formatDateTime($value['startTime']))
          ->bodyCell($value['@type'])
          ->bodyCell($value['result'] ? str_replace("&",'; ',$value['result']) : '')
	        ->bodyCell($value['actionStatus'])
          ->bodyCell($value['agent']['name'] ?? _("Undefined"))
          ->closeRow();
      }
    } else {
      $table->bodyCell(_("No data found!"), [ "colspan" => "4", "style" => "text-align: center;" ])->closeRow();
    }
    // READY
      return $table->ready();
    }
}
