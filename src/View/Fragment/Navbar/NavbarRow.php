<?php
namespace Plinct\Cms\View\Fragment\Navbar;

class NavbarRow
{
	/**
	 * @var array
	 */
	private array $wrapper = ['tag'=>'div', 'attributes'=>['class'=>'navbar-row']];
	/**
	 * @var array
	 */
	private array $items = [];
	/**
	 * @var int
	 */
	private int $level = 1;

	/**
	 * @param ?array ...$items
	 * @return $this
	 */
	public function setItems(?array ...$items): static
	{
		$this->items[] = $items;
		return $this;
	}

	/**]
	 * @param int $level
	 * @return $this
	 */
	public function setLevel(int $level): static
	{
		$this->level = $level;
		return $this;
	}

	/**
	 * @return array
	 */
	public function render(): array
	{
		$color = match ($this->level) {
			1 => "#444",
			2 => "#3e3e3e",
			3 => "#3a3a3a",
			4 => "#363636",
			5 => "#323232",
			6 => "#2e2e2e",
			default => "gray",
		};
		$this->wrapper['attributes']['style'] = "background-color: $color";
		$this->wrapper['content'] = $this->items;
		return $this->wrapper;
	}
}
