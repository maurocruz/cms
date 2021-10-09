<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Web\Element\Form as WebForm;

class Form extends FormAbstract implements FormInterface
{

    public function create(array $attributes = null): WebForm
    {
        $this->form = new WebForm($attributes);
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
        return self::selectReady('additionalType', self::getData(['class'=>$class]), $value);
    }

    /**
     * WRITE <SELECT> ELEMENT TO CHOOSE THE 'CATEGORY' OF A 'TYPE'
     *
     * @param string $class
     * @param string|null $value
     * @return array
     */
    public function selectCategory(string $class = "thing", string $value = null): array
    {
        return self::selectReady('category', self::getData(['class'=>$class,'source'=>'category']), $value);
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
        $form = new \Plinct\Web\Element\Form(['class'=>'form']);

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