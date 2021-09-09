<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class WebPageServer
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string
    {
        $data = Api::post('webPage',$params);
        $id = $params['isPartOf'];
        $item = $data['id'];
        return "/admin/webSite/webPage?id=$id&item=$item";
    }

    /**
     * @param array $params
     * @return string
     */
    public function edit(array &$params): string
    {
        // BREADCRUMB
        $params['breadcrumb'] = json_encode(Api::get('breadcrumb',['url'=>$params['url'],'alternativeHeadline'=>$params['alternativeHeadline']]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        Api::put('webPage', $params);

        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}
