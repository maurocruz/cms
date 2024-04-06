<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Box;

use Plinct\Web\Element\Element;

class Box implements BoxInterface
{
	/**
	 * @param $content
	 * @param string|null $caption
	 * @param array $attributes
	 * @return array
	 */
  public function simpleBox($content, string $caption = null, array $attributes = ['class'=>'box']): array
  {
   $div = new Element('div', $attributes);
    if ($caption) $div->content("<p>$caption</p>");
    $div->content($content);
    return $div->ready();
  }

  /**
   * @param string $caption
   * @param $content
   * @return array
   */
  public function expandingBox(string $caption, $content, bool $open = false, string $style = null): array
  {
    $id = "form-expanding-". mt_rand(111,999);
		$className = $open ? "button-dropdown button-dropdown-expanded" : "button-dropdown button-dropdown-contracted";
		$classBox = $open ? "box" : "box box-expanding";
    $div = new Element('div',['id'=> $id, 'class'=>$classBox, 'style'=>$style]);
    // CAPTION
    $div->content("<p class='$className' onclick='expandBox(this,\"$id\");'>$caption</p>");
    // CONTENT
    $div->content($content);
    // READY
    return $div->ready();
  }
}
