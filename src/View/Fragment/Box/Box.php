<?php
namespace Plinct\Cms\View\Fragment\Box;

use Plinct\Web\Element\Element;

class Box
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
	 * @param bool $open
	 * @param string|null $style
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

	public function expandigBoxWithoutContent(string $caption, string $class = null): string
  {
	  $id = "form-expanding-". mt_rand(111,999);
    $returns = "<div id='$id' class='box box-expanding $class' style='width: 100%;'>";
			$returns .= "<p class='button-dropdown button-dropdown-contracted' onclick='expandBox(this,\"$id\");' style='width: 100%;'>$caption</p>";
    return $returns;
  }
}
