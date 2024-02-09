<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Section\User;

use Plinct\Api\User\User;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Server\Api;

class UserController extends UserView
{
  /**
   * @param null $params
   */
  public function index($params = null)
  {
    $params = [ "format" => "ItemList" ];

    $search = filter_input(INPUT_GET,'search');
    if ($search) $params['nameLike'] = $search;
		$data = CmsFactory::request()->api()->get('user', $params);
var_dump($data);
		parent::indexView((new User())->get($params));
  }

  /**
   * @param array $params
   */
  public function edit(array $params)
  {
    $params = array_merge($params, [ "properties" => "email,create_time" ]);
    $data = (new User())->get($params);
		parent::editView($data[0]);
  }

  /**
   * @param null $params
   */
  public function new($params = null)
  {
		parent::newView($params);
  }

  /**
   * @param $status
   * @return string
   */
  public static function getStatusWithText($status): string
  {
    switch ($status) {
      case 1:
        return "administrator";
      default:
        return "user";
    }
  }
}
