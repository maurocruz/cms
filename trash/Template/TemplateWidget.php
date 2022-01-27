<?php

declare(strict_types=1);

namespace Plinct\Cms\Template;

use Plinct\Cms\App;
use Plinct\Cms\WebSite\Type\Structure\Header\HeaderView;

abstract class TemplateWidget extends TemplateAbstract
{
    /**
     * @return array
     */
    protected function navbarList(): array
    {
        $data = [ "/admin" => _("Home"), "/admin/user" => _("Users") ];

        if (App::getTypesEnabled()) {
            foreach (App::getTypesEnabled() as $key => $value) {
                $href = is_string($key) ? $key : "/admin/$value";
                $data[$href] = _(ucfirst($value));
            }
        }

        return $data;
    }

    /**
     * @param array $data
     */
    public function addNavBar(array $data)
    {
        $header = new HeaderView();
        $header->content([ "object"=>"navbar", "attributes" => $data['attributes'], "content" => $data['list'], "title" => $data['title'] ?? null, "append" => $data['append'] ?? null ]);
    }

    /**
     * @return array
     */
    protected static function formLogin(): array
    {
        return [ "tag" => "form", "attributes" => [ "action" => "/admin/login", "method" => "post", "class" => "form formPadrao" ], "content" => [
            [ "tag" => "h3", "content" => _("Log in") ],
            [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => _("Email") ],
                [ "tag" => "input", "attributes" => [ "name" => "email", "type" => "text" ] ]
            ]],
            [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => _("Password") ],
                [ "tag" => "input", "attributes" => [ "name" => "password", "type" => "password" ] ]
            ]],
            [ "tag" => "input", "attributes" => [ "name" => "submit", "type" => "submit", "value" => _("Send") ] ],
            [ "tag" => "p", "href" => "/admin/register", "content" => _("Make new registration") ]
        ] ];
    }

    /**
     * @return array
     */
    protected static function formRegister(): array
    {
        return [ "tag" => "form", "attributes" => [ "id" => "register-form", "action" => "/admin/register", "method" => "post", "class" => "form formPadrao", "onsubmit" => "return checkRegisterForm(this);" ], "content" => [
            [ "tag" => "h3", "content" => _("New user registration") ],
            [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => _("Name") ],
                [ "tag" => "input", "attributes" => [ "name" => "name", "type" => "text" ] ]
            ]],
            [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => _("Email") ],
                [ "tag" => "input", "attributes" => [ "name" => "email", "type" => "text" ] ]
            ]],
            [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => _("Password") ],
                [ "tag" => "input", "attributes" => [ "name" => "password", "type" => "password" ] ]
            ]],
            [ "tag" => "fieldset", "attributes" => [ "style" => "width: 100%;" ], "content" => [
                [ "tag" => "legend", "content" => _("Repeat the password") ],
                [ "tag" => "input", "attributes" => [ "name" => "passwordRepeat", "type" => "password" ] ]
            ]],
            [ "tag" => "input", "attributes" => [ "name" => "submit", "type" => "submit", "value" => _("Send") ] ]
        ]];
    }
}
