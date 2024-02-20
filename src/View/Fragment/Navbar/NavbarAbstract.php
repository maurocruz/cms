<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\Navbar;

abstract class NavbarAbstract
{
	/**
	 * @var string|null
	 */
	protected ?string $title;
	/**
	 * @var array|null
	 */
	protected ?array $tabs = null;
	/**
	 * @var int
	 */
	protected int $level = 2;
	/**
	 * @var array|null
	 */
	protected ?array $search = null;
  /**
   * @var ?string
   */
  protected ?string $type = null;
  /**
   * @var array
   */
  protected array $wrapper;

	/**
	 * @param string|null $title
	 * @return NavbarInterface
	 */
	public function setTitle(?string $title): NavbarInterface
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @param array|null $tabs
	 * @param array|null $attributes
	 * @return NavbarAbstract
	 */
	public function setTabs(?array $tabs, array $attributes = null): NavbarAbstract
	{
		if (is_array($tabs)) {
			foreach ($tabs as $link => $value) {
				$attr = $attributes ? array_merge(['href' => $link], $attributes) : ['href'=>$link];
				$this->tabs[] = ['tag' => 'a', 'attributes' => $attr, 'content' => $value];
			}
		}
		return $this;
	}

	/**
	 * @param int $level
	 * @return NavbarAbstract
	 */
	public function setLevel(int $level = 2): NavbarAbstract
	{
		$this->level = $level;
		return $this;
	}

	/**
	 * @param array|null $search
	 * @return NavbarInterface
	 */
	public function setSearch(?array $search): NavbarInterface
	{
		$this->search = $search;
		return $this;
	}

  /**
   * @param string $type
   */
  protected function setType(string $type): void
  {
    $this->type = $type;
  }

  /**
   */
  protected function setWrapper(): void
  {
    $this->wrapper = ['tag'=>'nav', 'attributes'=>['class'=>'menu']];
  }


  /**
   * @param string $name
   * @param $value
   */
  protected function setAttributes(string $name, $value): void
  {
    $this->wrapper['attributes'][$name] = $value;
  }

	/**
	 * @param $content
	 * @return NavbarInterface
	 */
  public function content($content): NavbarInterface
  {
    $this->wrapper['content'][] = $content;
    return $this;
  }
}
