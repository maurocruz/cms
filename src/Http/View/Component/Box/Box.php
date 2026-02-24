<?php
namespace Plinct\Cms\Http\View\Component\Box;

use Plinct\Web\Element\Element;

class Box
{
	private const string CLASS_DROPDOWN_CONTRACTED = 'button-dropdown button-dropdown-contracted';
	private const string CLASS_DROPDOWN_EXPANDED = 'button-dropdown button-dropdown-expanded';
	private const string CLASS_BOX_EXPANDING = 'box box-expanding';

	/**
	 * @return string
	 */
	private function generateRandomId(): string
	{
		return "form-expanding-" . mt_rand(111, 999);
	}

	/**
	 * @param $content
	 * @param string|null $caption
	 * @param string|null $idthing
	 * @param string|null $name
	 * @return array
	 */
	public function simpleBox($content, string $caption = null, ?string $idthing = null, ?string $name = null): array
	{
		$div = new Element('div', ['class' => 'box']);
		if ($caption) {
			$captionText = $caption;
			if ($idthing || $name) {
				$captionText .= " ($idthing): <span style='color: #eecc77; font-weight: bold;'>$name</span>";
			}
			$div->content("<p>$captionText</p>");
		}
		return $div->content($content)->ready();
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
		$id = $this->generateRandomId();
		$buttonClass = $open ? self::CLASS_DROPDOWN_EXPANDED : self::CLASS_DROPDOWN_CONTRACTED;
		$boxClass = $open ? "box" : self::CLASS_BOX_EXPANDING;
		$div = new Element('div', ['id' => $id, 'class' => $boxClass, 'style' => $style]);
		$div->content("<p class='$buttonClass' onclick='expandBox(this,\"$id\");'>$caption</p>");
		return $div->content($content)->ready();
	}

	/**
	 * @param string $caption
	 * @param string|null $class
	 * @param bool $open
	 * @return string
	 */
	public function expandingBoxWithoutContent(string $caption, string $class = null, bool $open = false): string
	{
		$id = $this->generateRandomId();
		$classBox = !$open ? trim(self::CLASS_BOX_EXPANDING . " $class") : "box ".$class;
		$div = new Element('div', [
			'id' => $id,
			'class' => $classBox,
			'style' => 'width: 100%;'
		]);
		$div->content("<p class='" . ($open ? self::CLASS_DROPDOWN_EXPANDED : self::CLASS_DROPDOWN_CONTRACTED) . "' onclick='expandBox(this,\"$id\");' style='width: 100%;'>$caption</p>");
		$elementData = $div->ready();
		$attributes = "";
		foreach ($elementData['attributes'] as $name => $value) {
			$attributes .= " $name='$value'";
		}
		return "<div$attributes>" . $elementData['content'][0];
	}
}
