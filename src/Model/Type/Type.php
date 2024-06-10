<?php
declare(strict_types=1);
namespace Plinct\Cms\Model\Type;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;

class Type
{
	/**
	 * @var string
	 */
	private string $type;

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @param array $params
	 * @return mixed|string|string[]
	 */
	public function post(array $params) {
		$action = $params['action'] ?? null;
		$data = CmsFactory::model()->api()->post($this->type, $params)->ready();
		// ERROR OR FAIL
		if ((isset($data['status']) && $data['status'] == 'fail') || (isset($data['error']))) {
			if(isset($data['error'])) {
				$data = $data['error'];
				$data['status'] = 'error';
			}
			return $data;
		}
		// SUCCESS
		else if (isset($data[0])) {
			$value = $data[0];
			$idname = "id$this->type";
			$idvalue = $value[$idname] ?? null;
			CmsFactory::view()->Logger('type')->info("NEW DATA: $this->type",['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser(),"type"=>$this->type, "params"=>$params]);
			// REDIRECT
			if ($this->type === "webPageElement" || $this->type === "programMembership" || $action === 'redirectToSamePage') {
				return filter_input(INPUT_SERVER, 'HTTP_REFERER');
			}
			// REDIRECT TO EDIT PAGE
			if ($idvalue) {
				return App::getURL() . dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $idvalue;
			} else {
				return filter_input(INPUT_SERVER, 'HTTP_REFERER');
			}
		}
		// UNKNOW RESPONSE
		else {
			CmsFactory::view()->Logger('type')->error("An error ocurred in Model/Type/type.php method post");
			return false;
		}
	}

	/**
	 * @param array $params
	 * @return mixed
	 */
	public function put(array $params) {
		$id = $params["id$this->type"];
		$namespaceClass = "Plinct\\Cms\\Controller\\Type\\".ucfirst($this->type)."\\".ucfirst($this->type);
		if (class_exists($namespaceClass)) {
			$classType = new $namespaceClass();
			if (method_exists($classType, 'update')) {
			$params =	$classType->update($params);
			}
		}
		$data = CmsFactory::model()->api()->put($this->type, $params)->ready();
		if ($data['status'] === "success") {
			CmsFactory::view()->Logger('type')->info("UPDATE DATA: $this->type",['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser(),"type"=>$this->type, "id"=>$id]);
		} elseif($data['status'] === 'fail') {
			CmsFactory::view()->Logger('type')->info("UPDATE FAIL: $this->type", array_merge(['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser()],$data));
		}
		return filter_input(INPUT_SERVER, 'HTTP_REFERER');
	}

	/**
	 * @param array $params
	 * @return mixed|string
	 */
	public function erase(array $params)
	{
		$id = $params["id$this->type"];
		$data = CmsFactory::model()->api()->delete($this->type, ["id$this->type" => $id])->ready();
		if ($data['status'] === 'success') {
			CmsFactory::view()->Logger('type')->info("ITEM DELETED", ['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser(),'type'=>$this->type, 'id'=>$id]);
		}
		// REDIRECT
		if ($this->type === "webPageElement" || $this->type === "programMembership") {
			return filter_input(INPUT_SERVER, 'HTTP_REFERER');
		}
		//
		return !array_search($this->type, App::getTypesEnabled()) ? '/admin/'.$this->type : filter_input(INPUT_SERVER, 'HTTP_REFERER');
	}
}
