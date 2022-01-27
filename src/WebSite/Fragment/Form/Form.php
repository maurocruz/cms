<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\Form;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Tool\ArrayTool;
use Plinct\Web\Element\ElementFactory;
use Plinct\Web\Element\Form\Form as WebForm;

class Form extends FormDecorator implements FormInterface
{
    /**
     * @param array|null $attributes
     * @return WebForm
     */
    public function create(array $attributes = null): WebForm
    {
        $this->form->attributes($attributes);
        return $this->form;
    }

    /**
     * WRITE <SELECT> ELEMENT TO CHOOSE THE 'ADDITIONAL TYPE' OF A 'TYPE'
     *
     * @param string $class
     * @param string|null $value
     * @return array
     */
    public function selectAdditionalType(string $class = "thing", string $value = null): array
    {
        return parent::selectReady('additionalType', parent::getData(['class'=>$class]), $value);
    }

    /**
     * WRITE <SELECT> ELEMENT TO CHOOSE THE 'CATEGORY' OF A 'TYPE'
     *
     * @param string $class
     * @param string|null $value
     */
    public function selectCategory(string $class = "thing", string $value = null)
    {
        $this->form->fieldset(self::selectReady('category', self::getData(['class'=>$class,'source'=>'category']), $value), _("Category"));
    }

    /**
     * WRITE <FORM> WITH SEARCH <INPUT> ELEMENT
     *
     * @param string $action
     * @param string $name
     * @param string|null $value
     * @return array
     */
    public function search(string $action, string $name, string $value = null): array
    {
        $form = ElementFactory::form(['class'=>'form']);
        // ACTION AND METHOD
        $form->action($action)->method('get');
        $form->content('<fieldset>');
        // CAPTION
        $form->content("<legend>"._("Search")."</legend>");
        // URI
        $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        if ($queryString) {
            parse_str($queryString, $queryArray);
            if ($queryArray) {
                foreach ($queryArray as $nameQuery => $valueQuery) {
                    $form->input($nameQuery, $valueQuery, "hidden");
                }
            }
        }
        // INPUT SEARCH
        $form->input($name, $value ?? '');
        // SUBMIT
        $form->input('', _("Submit") , 'submit');
        $form->content('</fieldset>');
        return $form->ready();
    }

    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param $propertyName
     * @param $tableIsPartOf
     * @param $value
     * @return array
     */
    public function relationshipOneToOne($tableHasPart, $idHasPart, $propertyName, $tableIsPartOf, $value = null): array
    {
        $table = lcfirst($tableIsPartOf);

        $this->form->attributes(["class" => "formPadrao form-relationship"]);
        $this->form->action("/admin/$tableHasPart/edit")->method("post");

        if ($value) {
            $id = ArrayTool::searchByValue($value['identifier'], "id")['value'];

            $this->form->input("id", $idHasPart, "hidden")
                ->fieldsetWithInput('name',$value['name'],_($value['@type']) . " <a href=\"/admin/$table/edit/$id\">"._("Edit")."</a>", "text", null, [ "disabled" ])
                ->input($propertyName, '', 'hidden')
                ->submitButtonDelete("/admin/$tableHasPart/edit");
        } else {
            $this->form->content("<div class='add-existent' data-type='$table' data-propertyName='$propertyName'', data-idHasPart='$idHasPart'></div>");
        }

        return $this->form->ready();
    }

    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param $tableIsPartOf
     * @param $value
     * @return array
     */
    public function relationshipOneToMany($tableHasPart, $idHasPart, $tableIsPartOf, $value = null): array
    {
        if ($value) {
            foreach ($value as $item) {
                $id = ArrayTool::searchByValue($item['identifier'], "id")['value'];
                $table = lcfirst($tableIsPartOf);
                $form = Fragment::form(["class" => "formPadrao"])
                    ->action("/admin/$table/edit")->method("post");
                $form->input("tableHasPart", $tableHasPart, "hidden")
                    ->input("idHasPart", $idHasPart, "hidden")
                    ->input("idIsPartOf", $id, "hidden")
                    ->fieldsetWithInput("name", $item['name'], _($item['@type']) . " <a href=\"/admin/$table/edit/$id\">".("edit this")."</a>", "text", null, ["disabled"])
                    ->submitButtonDelete("/admin/$table/erase");
                $return[] = $form->ready();
            }
        }
        $this->form->attributes(["class" => "formPadrao form-relationship"]);
        $this->form->action("/admin/" . lcfirst($tableIsPartOf) . "/new")->method("post");
        $this->form->input("tableHasPart", $tableHasPart, "hidden")
            ->input("idHasPart", $idHasPart, "hidden")
            ->content([ "tag" => "div", "attributes" => [ "class" => "add-existent", "data-type" => lcfirst($tableIsPartOf), "data-idHasPart" => $idHasPart  ] ]);

        $return[] = $this->form->ready();

        return $return;
    }
    /**
     * @param string $id
     * @param array $array
     * @return string
     */
    public function datalist(string $id, array $array): string
    {
        $content = null;
        foreach ($array as $value) {
            $content .= "<option value='$value'>";
        }
        return "<datalist id='$id'>$content</datalist>";
    }

    /**
     * Creates a type selection form and chooses the type from a pop-up in an input form
     * @param string $property
     * @param string|array $typesForChoose
     * @param array|bool $value
     * @param string $nameLike
     * @param array|null $attributes
     * @return array
     */
    public function chooseType(string $property, $typesForChoose, $value, string $nameLike = "name", array $attributes = []) : array
    {
        $attributes2['class'] = "choose-type";
        $attributes2['data-property'] = $property;
        $attributes2['data-types'] = is_array($typesForChoose) ? implode(",",$typesForChoose) : $typesForChoose;
        $attributes2['data-like'] = $nameLike;
        $attributes2['data-currentType'] = $value['@type'] ?? null;
        $attributes2['data-currentName'] = $value['name'] ?? null;
        $attributes2['data-currentId'] = isset($value['identifier']) ? ArrayTool::searchByValue($value['identifier'], "id")['value'] : null;
        $widthAttr = "display: flex; min-height: 23px;";
        $attributes2['style'] = array_key_exists('style', $attributes) ? $widthAttr." ".$attributes['style'] : $widthAttr;
        unset($attributes['style']);
        $attributes3 = $attributes ? array_merge($attributes2, $attributes) : $attributes2;
        return [ "tag" => "div", "attributes" => $attributes3 ];
    }
}
