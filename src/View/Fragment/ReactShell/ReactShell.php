<?php
namespace Plinct\Cms\View\Fragment\ReactShell;

use Plinct\Cms\Controller\App;

class ReactShell
{
	/**
	 * @var array
	 */
	private array $attributes = ['class'=>'plinct-shell'];
	/**
	 * @var array|string[]
	 */
	private ?array $columnsTable = null;

	/**
	 * @param string $type
	 * @param array $attributes
	 */
	public function __construct(string $type, array $attributes = [])	{
		$this->attributes['data-type'] = $type;
		$this->attributes = array_merge($this->attributes, $attributes);
	}


	public function setAction(string $name): ReactShell
	{
		$this->setAttribute('data-action', $name);
		return $this;
	}
	/**
	 * @param string $name
	 * @param ?string $value
	 * @return $this
	 */
	public function setDataset(string $name, ?string $value): ReactShell
	{
		$this->setAttribute("data-$name", $value);
		return $this;
	}

	/**
	 * @param bool $openSection
	 * @return ReactShell
	 */
	public function setOpenSection(bool $openSection): ReactShell
	{
		if ($openSection) {
			$this->setAttribute('openSection','true');
		}
		return $this;
	}

	/**
	 * @param string $value
	 * @return $this
	 */
	public function setOrderBy(string $value): ReactShell
	{
		$this->setAttribute("data-orderby", $value);
		return $this;
	}

	/**
	 * @param string $value
	 * @return $this
	 */
	public function setOrdering(string $value): ReactShell
	{
		$this->setAttribute("data-ordering", $value);
		return $this;
	}

	/**
	 * @param string $name
	 * @param int|string $value
	 * @return $this
	 */
	public function setAttribute(string $name, int|string $value ): ReactShell
	{
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * @param array $columnsTable
	 * @param bool $merge
	 * @return ReactShell
	 */
	public function setColumnsTable(array $columnsTable, bool $merge = true): ReactShell
	{
		if($merge) {
			$this->columnsTable = array_merge(["edit"=>"Edit","id"=>"id","name"=>"Nome"], $columnsTable, ['dateModified'=>"Modificado"]);
		} else {
			$this->columnsTable = $columnsTable;
		}
		return $this;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setTypeHasPart(string $name): ReactShell
	{
		$this->setAttribute("data-typehaspart", $name);
		return $this;
	}
	/**
	 * @param ?int $idHasPart
	 * @return $this
	 */
	public function setIdHasPart(?int $idHasPart): ReactShell
	{
		$this->setAttribute('data-idhaspart', $idHasPart);
		return $this;
	}

	/**
	 * @param ?int $idHasPart
	 * @return $this
	 */
	public function setIdIsPartOf(?int $idHasPart): ReactShell
	{
		$this->setAttribute('data-idispartof', $idHasPart);
		return $this;
	}

	/**
	 * @param string $id
	 * @return $this
	 */
	public function setId(string $id): ReactShell
	{
		$this->setAttribute('data-id', $id);
		return $this;
	}

	/**
	 * @param string $propertyName
	 * @param int|null $value
	 * @return $this
	 */
	public function getItemType(string $propertyName, int $value = null): ReactShell
	{
		$this->setAttribute('data-action','getItemType')
			->setAttribute('data-propertyName',$propertyName)
			->setAttribute('data-idispartof',(string) $value ?? '');
		return $this;
	}

	public function setProperty(string $name): ReactShell
	{
		$this->setDataset('property', $name);
		return $this;
	}

	/**
	 * @return string
	 */
	public final function ready(): string {
		$this->setAttribute('data-apihost', App::getApiHost());
		$div = "<div";
		foreach ($this->attributes as $key => $value) {
			$div .= " $key='$value'";
		}
		if ($this->columnsTable) {
			$columnsTable = htmlspecialchars(json_encode($this->columnsTable), ENT_QUOTES, 'UTF-8');
			$div .= " data-columnsTable='$columnsTable'";
		}
		$div .= "></div>";
		return $div;
	}
}
