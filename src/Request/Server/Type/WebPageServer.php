<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Server\Type;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\Server\Helpers\Helper;

class WebPageServer
{
    /**
     * @param $params
     * @return string
     */
    public function new($params): string
    {
        $params['breadcrumb'] = Helper::breadcrumb()->setPageUrl($params['url'],$params['alternativeHeadline'])->ready();

        $data = CmsFactory::request()->api()->post('webPage',$params)->ready();

        $id = $params['isPartOf'];
        $item = $data['id'];

        return "/admin/webSite/webPage?id=$id&item=$item";
    }

  /**
   * @param array $params
   * @return array
   */
  public function edit(array &$params): array
  {
    $params['breadcrumb'] = Helper::breadcrumb()->setPageUrl($params['url'],$params['alternativeHeadline'])->ready();
		return $params;
  }

    /**
     * @param array $params
     * @return string|void
     */
    public function erase(array $params)
    {
        $params['idwebPage'] = $params['id'];
        unset($params['id']);

        $response = CmsFactory::request()->api()->delete('webPage', $params)->ready();

        if (isset($response['error'])) {
            print_r([ "error" => [ "response" => $response ]]);
            die("Error message: {$response['error']['message']}}");
        } else {
            $id = $params['isPartOf'];
            return "/admin/webSite/webPage?id=$id";
        }
    }
}
