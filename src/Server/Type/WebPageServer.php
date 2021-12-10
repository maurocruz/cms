<?php

declare(strict_types=1);

namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;
use Plinct\Cms\Server\Helpers\Helper;

class WebPageServer
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string
    {
        $params['breadcrumb'] = Helper::breadcrumb()->setPageUrl($params['url'],$params['alternativeHeadline'])->ready();

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
        $params['breadcrumb'] = Helper::breadcrumb()->setPageUrl($params['url'],$params['alternativeHeadline'])->ready();

        Api::put('webPage', $params);

        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }

    /**
     * @param array $params
     * @return string|void
     */
    public function erase(array $params)
    {
        $params['idwebPage'] = $params['id'];
        unset($params['id']);

        $response = Api::delete('webPage', $params);

        if (isset($response['error'])) {
            print_r([ "error" => [ "response" => $response ]]);
            die("Error message: {$response['error']['message']}}");
        } else {
            $id = $params['isPartOf'];
            return "/admin/webSite/webPage?id=$id";
        }
    }
}
