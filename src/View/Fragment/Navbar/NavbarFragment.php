<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Navbar;

use Plinct\Cms\CmsFactory;

class NavbarFragment extends NavbarFragmentAbstract
{
  public function __construct()
  {
    $this->setWrapper();
  }

  /**
   * @param string $type
   * @return NavbarFragmentInterface
   */
  public function type(string $type): NavbarFragmentInterface
  {
    $this->setType($type);
    return $this;
  }

  /**
   * @param int $level
   * @return NavbarFragment
   */
  public function level(int $level): NavbarFragmentInterface
  {
    $this->setAttributes("class", "menu menu$level");
    return $this;
  }

  /**
   * @param string $link
   * @param string $text
   * @param array|null $attributes
   * @return NavbarFragmentInterface
   */
  public function newTab(string $link, string $text, array $attributes = null): NavbarFragmentInterface
  {
    $attributes['href'] = $link;
    $this->content([ 'tag' => 'a', 'attributes' => $attributes, 'content' => $text ]);
    return $this;
  }

  /**
   * @param string $title
   * @return NavbarFragmentInterface
   */
  public function title(string $title): NavbarFragmentInterface
  {
    $this->content("<h1>$title</h1>");
    return $this;
  }

  /**
   * @param string $action
   * @param string $searchBy
   * @param string|null $params
   * @param string|null $linkList
   * @return NavbarFragmentInterface
   */
  public function search(string $action, string $searchBy = 'name', string $params = null, string $linkList = null): NavbarFragmentInterface
  {
    $this->content("<div class='search-box' data-type='$this->type' data-action='$action' data-searchBy='$searchBy' data-params='$params' data-linkList='$linkList'></div>");
		CmsFactory::view()->addBundle('searchbox');
    return $this;
  }

  /**
   * @return array
   */
  public function ready(): array
  {
    return $this->wrapper;
  }
}
