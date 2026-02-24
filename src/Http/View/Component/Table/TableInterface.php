<?php
namespace Plinct\Cms\Http\View\Component\Table;

interface TableInterface
{
  /**
   * @param string $caption
   * @return TableInterface
   */
  public function caption(string $caption): TableInterface;

	/**
	 * @param string|null $caption
	 * @return TableInterface
	 */
  public function setCaption(?string $caption): TableInterface;

  /**
   * @param string ...$label
   * @return TableInterface
   */
  public function labels(string ...$label): TableInterface;

  /**
   * @param ...$list
   * @return TableInterface
   */
  public function addRow(... $list): TableInterface;

  /**
   * @param string $path
   * @return TableInterface
   */
  public function buttonEdit(string $path): TableInterface;

  /**
   * @return TableInterface
   */
  public function buttonDelete(string $type, string $idtype): TableInterface;

  /**
   * @param array $itemListElement
   * @param array $properties
   * @return Table
   */
  public function rows(array $itemListElement, array $properties): TableInterface;

  /**
   * @param string|null $pathToEditButton
   * @return Table
   */
  public function setEditButton(string $pathToEditButton = null): TableInterface;

  /**
   * @return array
   */
  public function ready(): array;
}
