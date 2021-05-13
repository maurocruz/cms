<?php
namespace Plinct\Cms\Template;

use Plinct\Web\Template\TemplateAbstract;

class TemplateWidget extends TemplateAbstract {

    protected static function formLogin(): array {
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

    protected static function formRegister(): array {
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