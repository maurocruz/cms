<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Person;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Type\View;
use Plinct\Tool\ArrayTool;

abstract class PersonViewAbstract
{
    /**
     * @var array
     */
    protected array $content;
    /**
     * @var string
     */
    protected string $id;
    /**
     * @var string
     */
    protected string $name;

    /**
     *
     */
    protected function navbarPerson()
    {
        View::contentHeader(Fragment::navbar()
            ->type('person')
            ->title(_("Person"))
            ->newTab('/admin/person', Fragment::icon()->home())
            ->newTab('/admin/person/new', Fragment::icon()->plus())
            ->level(2)
            ->search('/admin/person')
            ->ready()
        );
    }

    protected function navbarPersonEdit()
    {
        // LEVEL 1
        $this->navbarPerson();
        // LEVEL 2
        View::contentHeader(Fragment::navbar()
            ->type('person')
            ->title($this->name)
            ->level(3)
            ->newTab("/admin/person?id=$this->id", Fragment::icon()->home())
            ->newTab("/admin/person?id=$this->id&action=service", _("Services"))
            ->newTab("/admin/person?id=$this->id&action=product", _("Products"))
            ->ready()
        );
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
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

        $form = Fragment::form(["class" => "formPadrao form-person"]);
        $form->action("/admin/person/$case")->method('post');
        $form->content($content);
        $form->submitButtonSend();
        if ($case == 'edit') $form->submitButtonDelete("/admin/person/erase");
        return $form->ready();
    }
}
