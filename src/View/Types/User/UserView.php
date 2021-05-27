<?php
namespace Plinct\Cms\View\Types\User;

use Plinct\Cms\Controller\UserController;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Cms\View\Widget\navbarTrait;
use Plinct\Tool\ArrayTool;

class UserView {
    private $content = [];

    use navbarTrait;
    use FormElementsTrait;

    public function __construct() {
        $list = [ "/admin/user" => _("View all") ];
        $title = "Users";
        $level = 2;
        $append = self::searchPopupList("user");
        $this->content['navbar'][] = self::navbar($title, $list, $level, $append);
    }
    
    public function index(array $data): array {
        $this->content['main'][] = [ "tag" => "p", "content" => sprintf(_("Showing %s items!"), count($data) )];
        // thead
        $content[] = [ "tag" => "thead", "content" => [
            [ "tag" => "tr", "content" => [
                [ "tag" => "th", "content" => _("ID") ],
                [ "tag" => "th", "content" => _("Name") ],
                [ "tag" => "th", "content" => _("Status") ]
            ]]
        ]];
        // tbody
        $body = null;
        foreach ($data['itemListElement'] as $value) {
            $item = $value['item'];
            $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
            $body[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "content" => $id ],
                [ "tag" => "td", "content" => "<a href=\"/admin/user/edit/".$id."\">".$item['name']."</a>" ],
                [ "tag" => "td", "content" => _(UserController::getStatusWithText($item['status'])) ]
            ]];
        }
        $content[] = [ "tag" => "tbody", "content" => $body ];
        $this->content['main'][] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => $content ];
        return $this->content;
    }
    
    public function new(): array {
        $this->content['main'][] = self::divBox(_("Add new"), "User", [self::formUser()]);
        return $this->content;
    }

    public function edit(array $data): array {
       $this->content['main'][] = self::divBox(_("Edit user"), "User", [ self::formUser("edit", $data)]);
       return $this->content;
    }
    
    static private function formUser($case = 'new', $value = null): array {
        $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $content[] = $case == "edit" ? self::fieldsetWithInput("ID", "id", $id, [ "style" => "width: 40px"], "text", [ "readonly" ]) : null;
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name'] ?? null);
        $content[] = self::fieldsetWithInput(_("Email"), "email", $value['email'] ?? null);
        $statusText = _(UserController::getStatusWithText($value['status'] ?? null));
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => _("Status") ],
            [ "tag" => "select", "attributes" => [ "name" => "status" ], "content" => [
                [ "tag" => "option", "attributes" => [ "value" => $value['status'] ?? null ], "content" => $statusText ],
                [ "tag" => "option", "attributes" => [ "value" => "" ], "content" => _("Choose") ],
                [ "tag" => "option", "attributes" => [ "value" => "0" ], "content" => _("User") ],
                [ "tag" => "option", "attributes" => [ "value" => "1" ], "content" => _("Administrator") ]
            ]]
        ]];
        $content[] = $case == "edit" ? self::fieldsetWithInput(_("Created date"), "create_time", $value['create_time'] ?? null, null, "text", [ "readonly" ]) : null;
        $content[] = self::submitButtonSend();
        $content[] = $case =="edit" ? self::submitButtonDelete("/admin/user/erase") : null;
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/user/$case", "method" => "post"], "content" => $content ];
    }
}
