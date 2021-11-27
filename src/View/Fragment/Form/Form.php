<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Form;

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
     */
    public function selectAdditionalType(string $class = "thing", string $value = null)
    {
        $this->form->fieldset(parent::selectReady('additionalType', parent::getData(['class'=>$class]), $value), _("Additional type"));
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
}
