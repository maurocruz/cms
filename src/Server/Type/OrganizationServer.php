<?php
namespace Plinct\Cms\Server\Type;

use Plinct\Cms\Server\Api;

class OrganizationServer {

    public function new($params): string {
        if (isset($params['additionalType'])) {
            $params['additionalType'] = str_replace(" -> ",",",$params['additionalType']);
        }
        // API
        $data = Api::post("organization", $params);
        return dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
    }

    public function edit($params) {
        if (isset($params['additionalType'])) {
            $params['additionalType'] = str_replace(" -> ",",",$params['additionalType']);
        }
        Api::put('organization', $params);
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}