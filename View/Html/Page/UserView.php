<?php

namespace Plinct\Cms\View\Html\Page;

use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Auth\SessionUser;
use Plinct\Cms\View\Html\Piece\FormElementsTrait;
use Plinct\Cms\View\Html\Piece\navbarTrait;

class UserView
{
    private $content;

    use navbarTrait;
    use FormElementsTrait;

    public function __construct() {
        $list = [ "/admin/user" => _("View all"), "/admin/user/new" => _("Add new user") ];
        $title = "Users";
        $level = 2;
        $append = self::searchPopupList("user");

        $this->content['navbar'][] = self::navbar($title, $list, $level, $append);
    }
    
    public function index(array $data): array 
    {
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
            
            $id = PropertyValue::extractValue($item['identifier'], "id");
            
            $body[] = [ "tag" => "tr", "content" => [
                [ "tag" => "td", "content" => $id ],
                [ "tag" => "td", "content" => "<a href=\"/admin/user/edit/".$id."\">".$item['name']."</a>" ],
                [ "tag" => "td", "content" => _(SessionUser::getStatusWithString($item['status'])) ]
            ]];
        }
        $content[] = [ "tag" => "tbody", "content" => $body ];
        $this->content['main'][] = [ "tag" => "table", "attributes" => [ "class" => "table" ], "content" => $content ];
        return $this->content;
    }
    
    public function new(): array
    {
        $this->content['main'][] = self::divBox(_("Add new"), "User", [self::form()]);
        return $this->content;
    }


    public function edit(array $data): array 
    {
        
       $this->content['main'][] = self::divBox(_("Edit user"), "User", [ self::form("edit", $data)]);
       return $this->content;
    }
    
    static private function form($case = 'new', $value = null) 
    {
        $id = PropertyValue::extractValue($value['identifier'], "id");
        
        $content[] = $case == "edit" ? self::input("iduser", "hidden", $id) : null;
        
        $content[] = $case == "edit" ? self::fieldsetWithInput("ID", "iduser", $id, [ "style" => "width: 40px"], "text", [ "readonly" ]) : null;
        
        $content[] = self::fieldsetWithInput(_("Name"), "name", $value['name']);
        
        $content[] = self::fieldsetWithInput(_("Email"), "email", $value['email']);
        
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" => "legend", "content" => _("Status") ],
            [ "tag" => "select", "attributes" => [ "name" => "status" ], "content" => [
                [ "tag" => "option", "attributes" => [ "value" => $value['status'] ], "content" => SessionUser::getStatusWithString($value['status']) ],
                [ "tag" => "option", "attributes" => [ "value" => "" ], "content" => _("Choose") ],
                [ "tag" => "option", "attributes" => [ "value" => "0" ], "content" => _("User") ],
                [ "tag" => "option", "attributes" => [ "value" => "1" ], "content" => _("Administrator") ]
            ]]
        ]];
        //$content[] = self::fieldsetWithInput(_("Status"), "status", $value['status']);
        
        $content[] = self::fieldsetWithInput(_("Created date"), "create_time", $value['create_time'], null, "text", [ "readonly" ]);
        
        $content[] = self::submitButtonSend();
        
        $content[] = $case =="edit" ? self::submitButtonDelete("/admin/user/erase") : null;
        
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao", "action" => "/admin/user/$case", "method" => "post"], "content" => $content ];
    }
}
