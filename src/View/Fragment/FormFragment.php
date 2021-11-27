<?php

declare(strict_types=1);

namespace Plinct\Cms\View\Fragment;

use Plinct\Cms\Factory\ServerFactory;
use Plinct\Web\Element\Element;

class FormFragment // DEPRECATED
{
    /**
     * @param string $class
     * @param string|null $value
     * @return array
     */
    public static function selectAdditionalType(string $class = "thing", string $value = null): array
    {
        return self::selectReady('additionalType', self::getData(['class'=>$class]), $value);
    }

    /**
     * @param string $class
     * @param string|null $value
     * @return array
     */
    public static function selectCategory(string $class = "thing", string $value = null): array
    {
        return self::selectReady('category', self::getData(['class'=>$class,'source'=>'category']), $value);

    }

    /**
     * @param array $params
     * @return mixed
     */
    private static function getData(array $params) {
        $params = array_merge(['subClass'=>'true','format'=>'hierarchyText'], $params);
        $data = (ServerFactory::soloine())->get($params);
        return json_decode($data, true);
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
        $select->content("<option value=''>" . _("Select $property") . "</option>");

        if (isset($data['@graph'])) {
            foreach ($data['@graph'] as $key => $item) {
                $select->content("<option value='$key'>$item</option>");
            }
        }

        return $select->ready();
    }
}
