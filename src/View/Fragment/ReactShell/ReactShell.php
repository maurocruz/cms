<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Fragment\ReactShell;

use Plinct\Cms\CmsFactory;
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
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function setAttribute(string $name, string $value ): ReactShell
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
	 * @param string $tableName
	 * @return $this
	 */
	public function setTableHasPart(string $tableName): ReactShell
	{
		$this->setAttribute('data-tablehaspart', $tableName);
		return $this;
	}

	/**
	 * @param int $idHasPart
	 * @return $this
	 */
	public function setIsPartOf(int $idHasPart): ReactShell
	{
		$this->setAttribute('data-ispartof',(string) $idHasPart);
		return $this;
	}

	/**
	 * @return string
	 */
	public final function ready(): string {
		$this->setAttribute('data-apihost', App::getApiHost());
		//$this->attributes['data-usertoken'] = CmsFactory::controller()->user()->userLogged()->getToken();
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
