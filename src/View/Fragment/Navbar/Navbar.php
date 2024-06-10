<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Navbar;

use Plinct\Cms\Controller\App;

class Navbar extends NavbarAbstract implements NavbarInterface
{
  public function __construct(string $title = null, array $tabs = null, int $level = 2, array $searchInput = null)
  {
		$this->setTitle($title);
		$this->setTabs($tabs);
		$this->setLevel($level);
		$this->setSearch($searchInput);
		$this->setWrapper();
  }

  /**
   * @param string $type
   * @return NavbarInterface
   */
  public function type(string $type): NavbarInterface
  {
    $this->setType($type);
    return $this;
  }

	/**
	 * @param int $level
	 * @return NavbarInterface
	 */
  public function level(int $level): NavbarInterface
  {
		$this->level = $level;
    $this->setAttributes("class", "menu menu$level");
    return $this;
  }

  /**
   * @param string $link
   * @param string $text
   * @param array|null $attributes
   * @return NavbarInterface
   */
  public function newTab(string $link, string $text, array $attributes = null): NavbarInterface
  {
		$this->setTabs([$link => $text], $attributes);
    return $this;
  }

  /**
   * @param string $title
   * @return NavbarInterface
   */
  public function title(string $title): NavbarInterface
  {
		$this->setTitle($title);
    return $this;
  }

  /**
   * @param string $action
   * @param string $searchBy
   * @param string|null $params
   * @param string|null $linkList
   * @return NavbarInterface
   */
  public function search(string $action, string $searchBy = 'name', string $params = null, string $linkList = null): NavbarInterface
  {
		$this->setSearch(['action'=>$action,'searchBy'=>$searchBy,'params','linkList'=>$linkList]);
    return $this;
  }

  /**
   * @return array
   */
  public function ready(): array
  {
		$apiHost = App::getApiHost();
		// TITULO
		if ($this->title) {
			$this->content("<h1>$this->title</h1>");
		}
		// ATTRIBUTOS
	  $this->setAttributes("class", "menu menu$this->level");
		// TABS
		if ($this->tabs) {
			foreach ($this->tabs as $value) {
				$this->content($value);
			}
		}
		if ($this->search) {
			$searchBy = $this->search['searchBy'] ?? null;
			$params = $this->search['params'] ?? null;
			$linkList = $this->search['linkList'] ?? null;
			$this->content("<div class='search-box plinct-shell' data-action='searchNavbar' data-type='$this->type' data-searchBy='$searchBy' data-params='$params' data-linkList='$linkList' data-apihost='$apiHost'></div>");
		}
    return $this->wrapper;
  }
}
