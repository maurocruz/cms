<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Cms\Factory\ServerFactory;
use Plinct\Web\Element\Element;
use Plinct\Web\Element\Form;

class FormAbstract
{
    /**
     * @var Form
     */
    protected Form $form;


    /**
     * GET A DATA FROM SOLOINE SERVER
     *
     * @param array $params
     * @return mixed
     */
    protected static function getData(array $params) {
        $params = array_merge(['subClass'=>'true','format'=>'hierarchyText'], $params);
        return json_decode((ServerFactory::soloine())->get($params), true);
    }

    /**
     * WRITE A <SELECT> ELEMENT
     *
     * @param string $property
     * @param $data
     * @param null $value
     * @return array
     */
    protected static function selectReady(string $property, $data, $value = null): array
    {
        $select = new Element('select',['class'=>'select-soloine','name'=>$property]);

        if($value) {
            $select->content("<option value='$value'>$value</option>");
        }

        if (isset($data['@graph'])) {
            $select->content("<option value=''>" . _("Select $property") . "</option>");

            foreach ($data['@graph'] as $key => $item) {
                $select->content("<option value='$key'>$item</option>");
            }
        } else {
            $select->content("<option value=''>{$data['message']}</option>");
        }

        return $select->ready();
    }
}
