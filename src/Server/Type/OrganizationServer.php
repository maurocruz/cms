<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class OrganizationServer {

    public function edit($params) {
        $params['additionalType'] = str_replace(" -> ",",",$params['additionalType']);
        Api::put('organization', $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}