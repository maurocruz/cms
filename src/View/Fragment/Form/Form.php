<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment\Form;

use Plinct\Cms\Factory\ServerFactory;
use Plinct\Web\Element\Element;

class Form implements FormInterface
{

    public function selectAdditionalType(string $class = "thing", string $value = null): array
    {
        return self::selectReady('additionalType', self::getData(['class'=>$class]), $value);
    }

    public function selectCategory(string $class = "thing", string $value = null): array
    {
        return self::selectReady('category', self::getData(['class'=>$class,'source'=>'category']), $value);
    }

    /**
     * @param array $params
     * @return mixed
     */
    private static function getData(array $params) {
        $params = array_merge(['subClass'=>'true','format'=>'hierarchyText'], $params);
        return json_decode((ServerFactory::soloine())->get($params), true);
    }

    /**
     * @param string $property
     * @param $data
     * @param null $value
     * @return array
     */
    private static function selectReady(string $property, $data, $value = null): array
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