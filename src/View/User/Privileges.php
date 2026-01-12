<?php
namespace Plinct\Cms\View\User;

use Plinct\Cms\CmsFactory;

class Privileges
{
	private array $functionOptions = [
		'1'=>'visitor',
		'2'=>'collaborator',
		'3'=>'moderator',
		'4'=>'administrator',
		'5'=>'super'
	];

	/**
	 * @param $value
	 * @return array
	 */
	public function getPrivileges($value): array
	{
		$content = [];
		$privileges = $value['privileges'] ?? null;
		$userLoggedPrivilegesArray = CmsFactory::controller()->user()->userLogged()->getPrivileges();
		$userLoggedPrivileges = isset($userLoggedPrivilegesArray[0]) ? $userLoggedPrivilegesArray[0]['function'] : 1;
		$functionOptions = array_slice($this->functionOptions, 0, $userLoggedPrivileges, true);
		// TRANSLATE
		foreach ($functionOptions as $key => $item) {
			$this->functionOptions[$key] = _($item);
		}
		// USER PRIVILEGES
		if ($privileges) {
			foreach ($privileges as $valuePrivileges) {
				$content[] = CmsFactory::view()->fragment()->box()->simpleBox($this->privilegesForm('edit', $valuePrivileges), _('Edit'));
			}
		}
		// new
		if (!CmsFactory::controller()->user()->hasPrivileges($privileges,5,'crud','all')) {
			$content[] = CmsFactory::view()->fragment()->box()->simpleBox($this->privilegesForm('new', $value), _('Add new'));
		}
		// return
		return $content;
	}

	/**
	 * @param string $case
	 * @param array|null $value
	 * @return array
	 */
	private function privilegesForm(string $case = 'add', array $value = null): array
	{
		//var_dump($value);
		$iduser_privileges = $value['iduser_privileges'] ?? null;
		$iduser = $value['iduser'] ?? null;
		$function = $value['function'] ?? null;
		$action = $value['action'] ?? null;
		$namespace = $value['namespace'] ?? null;
		$userCreator = $value['userCreator'] ?? null;
		$form = CmsFactory::view()->fragment()->form("form-privileges",['class'=>'form-basic form-user-privileges'])
			->action("/admin/user/privileges/$case")->method('post');
		// HIDDEN
		$form->input('iduser', (string) $iduser, 'hidden');
		if ($iduser_privileges) {
			$form->input('iduser_privileges', (string) $iduser_privileges, 'hidden');
		}
		// function
		$form->fieldsetWithSelect('function', $function, $this->functionOptions, _('Function') );

		// ACTION
		$map = ['c'=>'create','r'=>'read','u'=>'update','d'=>'delete'];
		$valuesChecked = [];
		if ($action) {
			foreach (str_split($action) as $char) {
				if (isset($map[$char])) {
					$valuesChecked[$char] = $map[$char];
				}
			}
		}
		$form->fieldsetWithCheckbox('action', $map, $valuesChecked, _('Action'));

		// NAMESPACE
		$modulesEnabled = CmsFactory::controller()->configuration()->getModulesEnabled();
		$items = [];
		foreach ($modulesEnabled as $valueModule) {
			$items[$valueModule] = $valueModule;
		}
		$items['all'] = 'all';
		$valuesChecked = [];
		if (is_string($namespace)) {
			foreach (explode(',', $namespace) as $char) {
				if (in_array($char, $modulesEnabled)) {
					$valuesChecked[$char] = $char;
				}
			}
			if ($namespace && empty($valuesChecked)) {
				$valuesChecked[$namespace] = $namespace;
			}
		}
		$form->fieldsetWithCheckbox('namespace', $items, $valuesChecked, _('Namespace'));

		// buttons
		$form->submitButtonSend();
		if($case == 'edit') {
			$form->submitButtonDelete('/admin/user/privileges/erase');
		}
		//USE CREATOR
		if (is_array($userCreator)) {
			$form->content("<p class='userCreator'>". _('Privileged by:') . $userCreator['name']."</p>");
		}
		// ready
		return $form->ready();
	}
}
