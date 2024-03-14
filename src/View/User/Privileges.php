<?php
declare(strict_types=1);
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
		$content[] = CmsFactory::view()->fragment()->box()->simpleBox($this->privilegesForm('new', $value), _('Add new'));
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
		$iduser_privileges = $value['iduser_privileges'] ?? null;
		$iduser = $value['iduser'] ?? null;
		$function = $value['function'] ?? '1';
		$actions = $value['actions'] ?? 'r';
		$namespace = $value['namespace'] ?? 'public';

		$form = CmsFactory::view()->fragment()->form(['class'=>'formPadrao form-user-privileges'])
			->action("/admin/user/privileges/$case")->method('post');
		// HIDDEN
		$form->input('iduser', (string) $iduser, 'hidden');
		if ($iduser_privileges) {
			$form->input('iduser_privileges', (string) $iduser_privileges, 'hidden');
		}
		// function
		$form->fieldsetWithSelect('function', $function, $this->functionOptions, _('Function') );
		// actions
		$form->fieldsetWithInput('actions', $actions, _('Actions'));
		// namespace
		$form->fieldsetWithInput('namespace', $namespace, _('Namespace'));
		// use creator
		//$form->fieldsetWithInput('userCreator', $userCreator, _('User creator'));
		// buttons
		$form->submitButtonSend();
		if($case == 'edit') {
			$form->submitButtonDelete('/admin/user/privileges');
		}
		// ready
		return $form->ready();
	}
}
