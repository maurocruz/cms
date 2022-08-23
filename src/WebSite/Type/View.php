<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

use Plinct\Cms\CmsFactory;

class View extends ViewAbstract implements ViewInterface
{
  /**
   * @param $content
   * @return void
   */
  public static function contentHeader($content) {
		CmsFactory::webSite()->addHeader($content);
  }

  /**
   * @param string|null $title
   * @param array|null $list
   * @param int|null $level
   * @param array|null $searchInput
   */
  public static function navbar(string $title = null, array $list = null, int $level = null, array $searchInput = null)
  {
		$fragment = CmsFactory::response()->fragment()
			->navbar()
			->title($title)
			->level($level);
    if ($list) {
      foreach ($list as $key => $value) {
        $fragment->newTab($key, $value);
      }
    }

    if ($searchInput) {
      $type = $searchInput['table'] ?? null;
      if($type) $fragment->type($type);
      $fragment->search("/admin/$type/search",$searchInput['searchBy'] ?? "name", $searchInput['params'] ?? null, $searchInput['linkList'] ?? null);
    }

		CmsFactory::webSite()->addHeader($fragment->ready());
  }

  /**
   * @param $content
   */
  public static function main($content) {
		CmsFactory::webSite()->addMain($content);
  }

	/**
	 * @param $type
	 * @param $methodName
	 * @param $data
	 * @param $params
	 * @return void
	 */
  public function view($type, $methodName, $data, $params)
  {
    $className = __NAMESPACE__ . "\\" . ucfirst($type) . "\\" . ucfirst($type) . "View";

    // ERROR
    $error = $data['error'] ?? null;
    if ($error) {
      switch ($error['code']) {
        case '42S02':
          CmsFactory::webSite()->addMain(
						CmsFactory::response()->fragment()->error()->installSqlTable($type, $error['message']));
          break;
        default:
          CmsFactory::webSite()->addMain("<p class='warning'>Message: {$error['message']}</p>");
          CmsFactory::webSite()->addMain("<p class='warning'>Code: {$error['code']}</p>");
          CmsFactory::webSite()->addMain("<p class='warning'>Query: {$error['query']}</p>");
      }
    }

    // VIEW
    elseif (class_exists($className)) {
      $object = new $className();
      if (method_exists($object,$methodName)) {
        $object->{$methodName}($data, $params);
	    } else {
        CmsFactory::webSite()->addMain("<p class='warning'>Method view not exists!</p>");
      }
    }

    // TYPE NOT FOUND
    else {
      CmsFactory::webSite()->addMain("<p class='warning'>$type type view not founded</p>");
    }
  }
}
