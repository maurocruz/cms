<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite;

class WebSiteAbstract
{
  /**
   * @var array|string[]
   */
  protected static array $HTML = ['tag'=>'html'];
  /**
   * @var array|string[]
   */
  protected static array $HEAD = ['tag'=>'head'];
  /**
   * @var array|string[]
   */
  protected static array $BODY = ['tag'=>'body'];
  /**
   * @var array
   */
  protected static array $CONTENT = ['tag'=>'div', 'attributes'=>['class'=>'content']];
  /**
   * @var array
   */
  protected static array $HEADER = ['tag'=>'header', 'attributes'=>['class'=>'header'], 'content'=>[]];
  /**
   * @var array
   */
  protected static array $MAIN = ['tag'=>'main', 'attributes'=>['class'=>'main']];
  /**
   * @var array
   */
  protected static array $FOOTER = ['tag'=>'footer', 'attributes'=>['class'=>'footer']];
	/**
	 * @var array
	 */
	protected static array $BUNDLES = [];

  /**
   * @param $content
   * @return void
   */
  protected function addHead($content) {
    self::$HEAD['content'][] = $content;
  }

  /**
   * @param $content
   * @param bool $firstChild
   * @return void
   */
  public function addHeader($content, bool $firstChild = false) {
    if ($firstChild) {
      array_unshift(self::$HEADER['content'], $content);
    } else {
      self::$HEADER['content'][] = $content;
    }
  }

  /**
   * @param $content
   * @return void
   */
  public function addMain($content) {
    self::$MAIN['content'][] = $content;
  }

  /**
   * @param $content
   * @return void
   */
  protected function addFooter($content) {
    self::$FOOTER['content'][] = $content;
  }
}
