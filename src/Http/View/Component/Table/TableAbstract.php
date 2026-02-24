<?php
namespace Plinct\Cms\Http\View\Component\Table;

use Plinct\Web\Element\Table;

abstract class TableAbstract
{
  /**
   * @var Table
   */
  protected Table $table;
  /**
   * @var ?string
   */
  protected ?string $caption = null;
	/**
	 * @var string|null
	 */
	protected ?string $numberOfItems = null;
  /**
   * @var array
   */
  protected array $labels = [];
  /**
   * @var array
   */
  protected array $rows = [];
  /**
   * @var ?array
   */
  protected ?array $buttonEdit = null;
  /**
   * @var bool
   */
  protected bool $buttonDelete = false;
  /**
   * @var ?array
   */
  protected ?array $itemListElement = [];
  /**
   * @var array
   */
  protected array $properties = [];
  /**
   * @var bool
   */
  protected ?bool $editButton = null;
  /**
   * @var ?string
   */
  protected ?string $pathToEditButton = null;

	protected string $type;
	protected string $idtype;

	protected ?string $orderBy = null;
	protected ?string $ordering = null;

  /**
   * @param array|string[] $attributes
   */
  public function __construct(array $attributes = null)
  {
    $this->table = new Table($attributes);
  }

	/**
	 * @param string|null $caption
	 * @return TableInterface
	 */
	public function setCaption(?string $caption): TableInterface
	{
		$this->caption = $caption;
		return $this;
	}

	/**
	 * @param string $caption
	 * @return TableInterface
	 */
	public function caption(string $caption): TableInterface
	{
		$this->caption = $caption;
		return $this;
	}

	/**
	 * @param string ...$label
	 * @return TableInterface
	 */
	public function labels(string ...$label): TableInterface
	{
		$this->labels = func_get_args();
		return $this;
	}

	/**
   * @param array $properties
   * @return TableAbstract
   */
	public function setProperties(array $properties): TableAbstract
  {
    $this->properties = $properties;
		return $this;
  }

	/**
	 * @param string|null $numberOfItems
	 * @return TableAbstract
	 */
	public function setNumberOfItems(?string $numberOfItems): TableAbstract
	{
		$this->numberOfItems = $numberOfItems;
		return $this;
	}


	/**
	 * @param ...$list
	 * @return TableInterface
	 */
	public function addRow(...$list): TableInterface
	{
		$this->rows[] = func_get_args();
		return $this;
	}

	public function buttonEdit(string $path): TableInterface
	{
		$this->buttonEdit[] = $path;
		return $this;
	}

	public function buttonDelete(string $type, string $idType): TableInterface
	{
		$this->type = $type;
		$this->idtype = $idType;
		$this->buttonDelete = true;
		return $this;
	}

	/**
	 * @param ?array $itemListElement
	 * @param array $properties
	 * @return TableInterface
	 */
	public function rows(?array $itemListElement, array $properties): TableInterface
	{
		$this->properties = $properties;
		$this->itemListElement = $itemListElement;
		return $this;
	}

	/**
	 * @param string|null $pathToEditButton
	 * @return TableInterface
	 */
	public function setEditButton(string $pathToEditButton = null): TableInterface
	{
		$this->editButton = true;
		$this->pathToEditButton = $pathToEditButton;
		return $this;
	}

	/**
	 * @param string $orderBy
	 * @return TableInterface
	 * */
	public function setOrderBy(string $orderBy): TableInterface
	{
		$this->orderBy = $orderBy;
		return $this;
	}

	/**
	 * @param string $ordering
	 * @return TableInterface
	 */
	public function setOrdering(string $ordering): TableInterface
	{
		$this->ordering = $ordering;
		return $this;
	}
}
