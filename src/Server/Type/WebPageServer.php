<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class WebPageServer {

    public function new($params): string {
        $data = Api::post('webPage',$params);
        $id = $params['isPartOf'];
        $item = $data['id'];
        return "/admin/webSite/webPage?id=$id&item=$item";
    }
}