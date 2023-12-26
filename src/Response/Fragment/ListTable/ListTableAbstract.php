<?php
declare(strict_types=1);
namespace Plinct\Cms\Response\Fragment\ListTable;

use Plinct\Web\Element\Table;

abstract class ListTableAbstract
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
   * @var array
   */
  protected array $itemListElement = [];
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

  protected string $idIsPartOf;
  protected string $tableIsPartOf;
  protected string $idHasPart;
  protected string $tableHasPart;

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
	 * @return ListTableInterface
	 */
	public function setCaption(?string $caption): ListTableInterface
	{
		$this->caption = $caption;
		return $this;
	}

	/**
	 * @param string $caption
	 * @return ListTableInterface
	 */
	public function caption(string $caption): ListTableInterface
	{
		$this->caption = $caption;
		return $this;
	}

	/**
	 * @param string ...$label
	 * @return ListTableInterface
	 */
	public function labels(string ...$label): ListTableInterface
	{
		$this->labels = func_get_args();
		return $this;
	}

	/**
   * @param array $properties
   * @return ListTableAbstract
   */
	public function setProperties(array $properties): ListTableAbstract
  {
    $this->properties = $properties;
		return $this;
  }

	/**
	 * @param string|null $numberOfItems
	 * @return ListTableAbstract
	 */
	public function setNumberOfItems(?string $numberOfItems): ListTableAbstract
	{
		$this->numberOfItems = $numberOfItems;
		return $this;
	}


	/**
	 * @param ...$list
	 * @return ListTableInterface
	 */
	public function addRow(...$list): ListTableInterface
	{
		$this->rows[] = func_get_args();
		return $this;
	}

	public function buttonEdit(string $path): ListTableInterface
	{
		$this->buttonEdit[] = $path;
		return $this;
	}

	public function buttonDelete(string $idIsPartOf, string $tableIsPartOf, string $idHasPart = null, string $tableHasPart = null): ListTableInterface
	{
		$this->idIsPartOf = $idIsPartOf;
		$this->tableIsPartOf = $tableIsPartOf;
		$this->idHasPart = $idHasPart;
		$this->tableHasPart = $tableHasPart;
		$this->buttonDelete = true;
		return $this;
	}

	/**
	 * @param array $itemListElement
	 * @param array $properties
	 * @return ListTable
	 */
	public function rows(array $itemListElement, array $properties): ListTableInterface
	{
		$this->properties = $properties;
		$this->itemListElement = $itemListElement;
		return $this;
	}

	/**
	 * @param string|null $pathToEditButton
	 * @return ListTableInterface
	 */
	public function setEditButton(string $pathToEditButton = null): ListTableInterface
	{
		$this->editButton = true;
		$this->pathToEditButton = $pathToEditButton;
		return $this;
	}

	/**
	 * @param string $orderBy
	 * @return ListTableInterface
	 * */
	public function setOrderBy(string $orderBy): ListTableInterface
	{
		$this->orderBy = $orderBy;
		return $this;
	}

	/**
	 * @param string $ordering
	 * @return ListTableInterface
	 */
	public function setOrdering(string $ordering): ListTableInterface
	{
		$this->ordering = $ordering;
		return $this;
	}
}
