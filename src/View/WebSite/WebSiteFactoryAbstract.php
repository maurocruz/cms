<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite;

class WebSiteFactoryAbstract
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
   * @return void|null
   */
  public static function addHeader($content, bool $firstChild = false) {
    if ($firstChild) {
      array_unshift(self::$HEADER['content'], $content);
    } else {
      self::$HEADER['content'][] = $content;
    }
		return null;
  }

	/**
	 * @param $content
	 * @return null
	 */
  public static function addMain($content): ?bool
  {
    self::$MAIN['content'][] = $content;
		return true;
  }

  /**
   * @param $content
   * @return null
   */
  protected static function addFooter($content) {
    self::$FOOTER['content'][] = $content;
		return null;
  }


	/**
	 * @param string $bundle
	 * @return null
	 */
	public static function addBundle(string $bundle)
	{
		if (in_array($bundle,self::$BUNDLES) === false) {
			self::$BUNDLES[] = $bundle;
		}
		return null;
	}
}
