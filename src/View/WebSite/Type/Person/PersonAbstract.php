<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\Person;

use Plinct\Cms\CmsFactory;

abstract class PersonAbstract
{
  /**
   * @var array
   */
  protected array $content;
  /**
   * @var int
   */
  protected int $id;
  /**
   * @var string
   */
  protected string $name = '';

  /**
   *
   */
  protected function navbarPerson()
  {
		CmsFactory::View()->addHeader(
			CmsFactory::View()->fragment()->navbar()
        ->type('person')
        ->title(_("Person"))
        ->newTab('/admin/person', CmsFactory::View()->fragment()->icon()->home())
        ->newTab('/admin/person/new', CmsFactory::View()->fragment()->icon()->plus())
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
    CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
      ->type('person')
      ->title($this->name)
      ->level(3)
      ->newTab("/admin/person/edit/$this->id", CmsFactory::view()->fragment()->icon()->home())
      //->newTab("/admin/person?id=$this->id&action=service", _("Services"))
     // ->newTab("/admin/person?id=$this->id&action=product", _("Products"))
      ->ready()
    );
  }

  /**
   * @param string $case
   * @param null $value
   * @param null $tableHasPart
   * @param null $idHasPart
   * @return array
   */
  protected function formPerson(string $case = 'new', $value = null, $tableHasPart = null, $idHasPart = null): array
  {
    $id = isset($value) ? $this->id : null;

    $content[] = $case == "edit" ? [ "tag" => "input", "attributes" => [ "name"=>"idperson", "type" => "hidden", "value" => $id ] ] : null ;
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

    $form = CmsFactory::view()->fragment()->form(["class" => "formPadrao form-person"]);
    $form->action("/admin/person/$case")->method('post');
    $form->content($content);
    $form->submitButtonSend();
    if ($case == 'edit') $form->submitButtonDelete("/admin/person/erase");
    return $form->ready();
  }
}
