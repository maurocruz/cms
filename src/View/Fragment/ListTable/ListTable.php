<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\ListTable;

use Plinct\Cms\CmsFactory;

class ListTable extends ListTableAbstract implements ListTableInterface
{
  /**
   * @param array|string[] $attributes
   */
  public function __construct(array $attributes = null)
  {
    parent::__construct($attributes);
  }

	/**
	 * @return void
	 */
	protected function buildLabels()
	{
		// BUTTON EDIT
		if ($this->editButton || $this->buttonEdit) {
			$this->table->head(_("Edit"), ['style'=>'width: 50px;']);
		}
		// COLUMN LABELS
		foreach ($this->labels as $key => $columnLabel) {
			if ($this->orderBy && $this->ordering) {
				$orderBy = $this->properties[$key];
				$ordering = $this->ordering == 'asc' ? 'desc' : 'asc';
				$content = "<a href='?orderBy=$orderBy&ordering=$ordering'>$columnLabel</a>";
			} else {
				$content = $columnLabel;
			}
			$this->table->head($content);
		}
		// BUTTON DELETE
		if ($this->buttonDelete) {
			$this->table->head(_("Delete"), ['style'=>'width: 50px;']);
		}
	}

	/**
	 * @return void
	 */
	protected function buildCaption()
	{
		$numberShowing = $this->table->getNumberOfRows();
		$numberOfItems = $this->numberOfItems ?? $numberShowing;
		$caption = "<h1>$this->caption</h1>";
		$caption .= "<p>" . sprintf(_("Showing %s of %s items!"), "<span>$numberShowing</span>", "<span>$numberOfItems</span>") . "</p>";
		$this->table->caption($caption);
	}

	/**
	 *
	 */
	protected function buildRows() {
		if (!empty($this->itemListElement)) {
			foreach ($this->itemListElement as $itemList) {
				$item = $itemList['item'] ?? $itemList;
				$id = $item['id'.lcfirst($item['@type'])];
				if ($this->editButton || $this->buttonEdit) {
					$this->table->bodyCell(CmsFactory::view()->fragment()->icon()->edit(), ['style' => 'text-align: center;'], ($this->buttonEdit ?? $this->pathToEditButton) . $id);
				}
				foreach ($this->properties as $property) {
					$explode = explode(":",$property);
					$bodyCell = $item;
					foreach ($explode as $prop) {
						if(is_array($bodyCell) && array_key_exists($prop, $bodyCell)) {
							$bodyCell = $bodyCell[$prop]['name'] ?? $bodyCell[$prop];
						}
					}
					$this->table->bodyCell($bodyCell);
				}
				if ($this->buttonDelete) {
					$this->table->bodyCell(CmsFactory::view()->fragment()->icon()->delete(), ['style' => 'text-align: center;'], $this->buttonDelete . $id);
				}
				$this->table->closeRow();
			}
		}
		// AS ADD ROW
		if (!empty($this->rows)) {
			foreach ($this->rows as $key => $row) {
				// edit buttom
				if ($this->buttonEdit) {
					$this->table->bodyCell(CmsFactory::view()->fragments()->icon()->edit(), ['style' => 'text-align: center;'], $this->buttonEdit[$key]);
				}
				// items
				foreach ($row as $cell) {
					$this->table->bodyCell($cell);
				}
				// delete buttom
				if ($this->buttonDelete) {
					$this->table->bodyCell(CmsFactory::view()->fragment()->buttons()->buttonDelete($this->idIsPartOf, $this->tableIsPartOf, $this->idHasPart, $this->tableHasPart),['style' => 'text-align: center;']);
				}
				// close
				$this->table->closeRow();
			}

		}
		// NO ITEMS FOUND
		if ($this->table->getNumberOfRows() === 0) {
			$countLabels = count($this->labels);
			$colspan = $this->editButton ? $countLabels + 1 : $countLabels;
			$this->table->bodyCell(_("No items found!"),['colspan'=>"$colspan",'style'=>'text-align: center; font-size:120%; font-weight: bold; color: yellow;'])->closeRow();
		}
	}
  /**
   * @return array
   */
  public function ready(): array
  {
    // LABELS COLUMNS
    $this->buildLabels();
    // ROWS
    $this->buildRows();
    // CAPTION
    $this->buildCaption();
    // READY
    return $this->table->ready();
  }
}
