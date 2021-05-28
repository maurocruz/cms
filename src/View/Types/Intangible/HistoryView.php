<?php
namespace Plinct\Cms\View\Types\Intangible;

use Plinct\Tool\DateTime;
use Plinct\Web\Element\Table;

class HistoryView {

    public function view($data): array {
        // TABLE
        $table = new Table();
        // HEADERS
        $table->head(_("Date"), [ "style" => "width: 160px;" ])
            ->head(_("Action"), [ "style" => "width: 80px;" ])
            ->head(_("Summary"))
            ->head(_("Author"), [ "style" => "width: 150px;" ]);
        // BODY
        if($data) {
            foreach ($data as $value) {
                $table->bodyCell(DateTime::formatDateTime($value['datetime']))
                    ->bodyCell($value['action'])
                    ->bodyCell(stripslashes($value['summary']))
                    ->bodyCell($value['user']['name'])
                    ->closeRow();
            }
        } else {
            $table->bodyCell(_("No data found!"), [ "colspan" => "4", "style" => "text-align: center;" ])->closeRow();
        }
        // READY
        return $table->ready();
    }
}
