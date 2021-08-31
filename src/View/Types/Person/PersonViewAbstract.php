<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Types\Person;

use Plinct\Cms\View\Fragment\Fragment;
use Plinct\Cms\View\Structure\Header\HeaderView;
use Plinct\Cms\View\Widget\FormElementsTrait;
use Plinct\Tool\ArrayTool;

abstract class PersonViewAbstract
{
    protected $content;
    protected $id;
    protected $name;

    use FormElementsTrait;

    /**
     *
     */
    protected function navbarPerson()
    {
        HeaderView::navbar( _("Person"), [
                '/admin/person'=> Fragment::icon()->home(),
                '/admin/person/new'=> Fragment::icon()->plus()
            ], 2, ['table'=>'person']);
    }

    protected function navbarPersonEdit()
    {
        // LEVEL 1
        $this->navbarPerson();
        // LEVEL 2
        HeaderView::navbar($this->name, [
            "/admin/person?id=$this->id"=> Fragment::icon()->home(),
            "/admin/person?id=$this->id&action=service"=>_("Services"),
            "/admin/person?id=$this->id&action=product"=>_("Products")
        ],3);
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $case
     * @param null $value
     * @param null $tableHasPart
     * @param null $idHasPart
     * @return array
     */
    protected static function formPerson(string $case = 'new', $value = null, $tableHasPart = null, $idHasPart = null): array
    {
        $id = isset($value) ? ArrayTool::searchByValue($value['identifier'], 'id')['value'] : null;
        $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name"=>"id", "type" => "hidden", "value" => $id ] ] : null ;
        if ($tableHasPart) {
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"tableHasPart", "type" => "hidden", "value"=>$tableHasPart ] ] ;
            $content[] = [ "tag" => "input", "attributes" => [ "name"=>"idHasPart", "type" => "hidden", "value"=>$idHasPart ] ] ;
        }
        // GIVEN NAME
        $attributes = [ "name"=>"givenName", "type" => "text", "value" => $value['givenName'] ?? "" ];
        $attr = $case !== "edit" ? array_merge($attributes, [ "data-idselect" => "gv".($id ?? $case), "onKeyUp" => "selectItemFormBd(this,'person');", "autocomplete" => "off" ]) : $attributes;
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Given name") ],
            [ "tag" => "input", "attributes" => $attr ]
        ]];
        // family name
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Family name") ],
            [ "tag" => "input", "attributes" => [ "name"=>"familyName", "type" => "text", "value"=>$value['familyName'] ?? null ] ]
        ]];
        // additional name
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Additional name") ],
            [ "tag" => "input", "attributes" => [ "name"=>"additionalName", "type" => "text", "value"=>$value['additionalName'] ?? null ] ]
        ]];
        // Tax ID
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Tax ID") ],
            [ "tag" => "input", "attributes" => [ "name"=>"taxId", "type" => "text", "value"=>$value['taxId'] ?? null ] ]
        ]];
        // birthdate
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Birth date") ],
            [ "tag" => "input", "attributes" => [ "name"=>"birthDate", "type" => "date", "value"=>$value['birthDate'] ?? null ] ]
        ]];
        // birthplace
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Birth place") ],
            [ "tag" => "input", "attributes" => [ "name"=>"birthPlace", "type" => "text", "value"=>$value['birthPlace'] ?? null ] ]
        ]];
        // gender
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Gender") ],
            [ "tag" => "input", "attributes" => [ "name"=>"gender", "type" => "text", "value"=>$value['gender'] ?? null ] ]
        ]];
        // has occupation
        $content[] = [ "tag" => "fieldset", "content" => [
            [ "tag" =>"legend", "content" => _("Has occupation") ],
            [ "tag" => "input", "attributes" => [ "name"=>"hasOccupation", "type" => "text", "value" => $value['hasOccupation'] ?? null ] ]
        ]];

        // submit
        $content[] = self::submitButtonSend();
        if ($case !== "add") {
            $content[] = self::submitButtonDelete("/admin/person/erase");
        }
        return [ "tag" => "form", "attributes" => [ "class" => "formPadrao form-person", "action" => "/admin/person/$case", "method" => "post"], "content" => $content ];
    }


}