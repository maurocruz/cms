<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\User;

use Plinct\Cms\Controller\UserController;
use Plinct\Cms\Factory\ViewFactory;
use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Structure\Main\MainView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

class UserView
{
    use FormElementsTrait;

    public function navbarUser(string $title = null)
    {
        ViewFactory::headerNavbar(_("Users"), [
            "/admin/user"=> Fragment::icon()->home(),
            "/admin/user/new"=> Fragment::icon()->plus()
        ], 2, ['table'=>'user']);

        if ($title) ViewFactory::headerNavbar($title, [], 3);
    }
    
    public function index(array $data)
    {
        // navbar
        $this->navbarUser();
        // showing
        MainView::content([ "tag" => "p", "content" => sprintf(_("Showing %s items!"), count($data) )]);

        $list = Fragment::listTable();

        $list->caption(_("Users"));

        $list->labels(_("Name"), 'Status');

        $list->rows($data['itemListElement'],['name','status']);

        $list->setEditButton('/admin/user/edit/');

        // TODO inserir aqui botão delete do ListTableFragment()

        MainView::content($list->ready());

    }

    /**
     * @param null $data
     */
    public function new($data = null)
    {
        $this->navbarUser();

        MainView::content(self::divBox(_("Add new"), "User", [self::formUser()]));
    }

    /**
     * @param array $data
     */
    public function edit(array $data)
    {
        $this->navbarUser($data['name']);

       MainView::content(self::divBox(_("Edit user"), "User", [ self::formUser("edit", $data)]));
    }

    /**
     * @param string $case
     * @param null $value
     * @return array
     */
    static private function formUser(string $case = 'new', $value = null): array
    {
        $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $content[] = $case == "edit" ? self::fieldsetWithInput("ID", "id", $id, null, "text", [ "readonly" ]) : null;
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
