<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Box;

interface BoxInterface
{
	/**
	 * @param $content
	 * @param string|null $caption
	 * @param array $attributes
	 * @return array
	 */
    public function simpleBox($content, string $caption = null, array $attributes = ['class'=>'box']): array;

  /**
   * @param string $caption
   * @param $content
   * @return array
   */
  public function expandingBox(string $caption, $content): array;
}
