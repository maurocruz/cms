<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Error;

class Error implements ErrorInterface
{
    /**
     * @param string $type
     * @param string $message
     * @return array
     */
    public function installSqlTable(string $type, string $message): array
    {
        return [ "tag" => "div", "attributes"=> [ "class" => "warning"], "content" => [
            [ "tag" => "p", "content" => "Message: " . _($message) ],
            [ "tag" => "form", "attributes" => [ "action" => "/admin/$type/createSqlTable", "method" => "post" ], "content" => [
                [ "tag" => "input", "attributes" => [ "type" => "submit", "value" => _("Do you want to install it?") ] ]
            ]]
        ]];
    }
}