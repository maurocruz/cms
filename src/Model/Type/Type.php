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
		$data = CmsFactory::model()->api()->post($this->type, $params)->ready();
		if ((isset($data['status']) && $data['status'] == 'fail') || (isset($data['error']))) {
			if(isset($data['error'])) {
				$data = $data['error'];
				$data['status'] = 'error';
			}
			return $data;
		} else {
			$id = $data['id'];
			CmsFactory::view()->Logger('type')->info("UPDATE DATA: $this->type",['uid'=>CmsFactory::controller()->user()->userLogged()->getIduser(),"type"=>$this->type, "id"=>$id]);
		}
		// REDIRECT TO EDIT PAGE
		if (isset($data['id']) && !isset($params['tableHasPart'])) {
			return App::getURL() . dirname(filter_input(INPUT_SERVER, 'REQUEST_URI')) . DIRECTORY_SEPARATOR . "edit" . DIRECTORY_SEPARATOR . $data['id'];
		}
		return filter_input(INPUT_SERVER, 'HTTP_REFERER');
	}

	/**
	 * @param array $params
	 * @return mixed
	 */
	public function put(array $params) {
		$id = $params["id$this->type"];
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
		return !array_search($this->type, App::getTypesEnabled()) ? filter_input(INPUT_SERVER, 'HTTP_REFERER') : dirname(filter_input(INPUT_SERVER, 'REQUEST_URI'));
	}
}