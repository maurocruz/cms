<?php
namespace Plinct\Cms\Model\Type;

use Exception;
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
	 * @param array|null $params
	 * @return array
	 * @throws Exception
	 */
	public function get(?array $params): array
	{
		return CmsFactory::model()->api()->get($this->type, $params)->ready();
	}

	/**
	 * @param array $params
	 * @param array|null $queryParams
	 * @return mixed|string|string[]
	 * @throws Exception
	 */
	public function post(array $params, array $queryParams = null): mixed
	{
		$isMultidimensional = array_reduce($params,function ($params, $item) { return is_array($item); });
		if ($isMultidimensional) {
			$newParams['multidimensional'] = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
			$params = $newParams;
		}
		$data = CmsFactory::model()->api()->post($this->type, $params)->ready();
		// ERROR OR FAIL
		if (array_key_exists('status', $data) && ($data['status'] == 'fail' || $data['status'] == 'error')) {
			return $data;
		}
		// SUCCESS
		else if (array_key_exists('status', $data) && $data['status'] == "success" ) {
			$value = $data['data'][0];
			$idvalue = is_array($value) ? ($value["id".lcfirst($this->type)] ?? null) : null;
			CmsFactory::view()->Logger('type')->info("NEW DATA: $this->type",['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser(),"type"=>$this->type, "params"=>$params]);
			// REDIRECT
			$redirectedPage = ['orderItem','programMembership','webPageElement','invoice','contactPoint','propertyValue','postalAddress'];
			if (in_array($this->type, $redirectedPage)) {
				return filter_input(INPUT_SERVER, 'HTTP_REFERER');
			}
			// REDIRECT TO EDIT PAGE
			if ($idvalue) {
				return App::getURL() . dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $idvalue . "?" . http_build_query($queryParams);
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
	 * @throws Exception
	 */
	public function put(array $params): mixed
	{
		$id = $params["id$this->type"];
		$namespaceClass = "Plinct\\Cms\\Controller\\Type\\".ucfirst($this->type)."\\".ucfirst($this->type).'Controller';
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
	 * @throws Exception
	 */
	public function erase(array $params): mixed
	{
		$data = CmsFactory::model()->api()->delete($this->type, $params)->ready();
		// logger
		CmsFactory::view()->Logger('type')->info("DELETE ".$data['status'], ['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser(),'type'=>$this->type]);
		// Relationship
		if ($data['status'] === 'success' && $data['message'] == 'Relationships deleted') {
			return filter_input(INPUT_SERVER, 'HTTP_REFERER');
		}
		// TODO fazer redirecionameto para offer
		// REDIRECT
		$redirectedPage = ['orderItem','programMembership','webPageElement','invoice','propertyValue','role'];
		if (in_array($this->type, $redirectedPage)) {
			if ($this->type == 'invoice' && (isset($params['output']) && $params['output'] == 'redirect_home')) {
				return '/admin/order/edit/'.$params['referencesOrder'];
			}
			return filter_input(INPUT_SERVER, 'HTTP_REFERER');
		}
		//
		return !array_search($this->type, CmsFactory::controller()->configuration()->getModulesEnabled()) ? '/admin/'.$this->type : filter_input(INPUT_SERVER, 'HTTP_REFERER');
	}
}
