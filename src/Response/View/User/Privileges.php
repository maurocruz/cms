<?php

declare(strict_types=1);

namespace Plinct\Cms\Response\View\User;

use Plinct\Cms\CmsFactory;

class Privileges
{
	const FUNCTIONS = [
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
		if ($privileges) {
			foreach ($privileges as $valuePrivileges) {
				$content[] = CmsFactory::response()->fragment()->box()->simpleBox($this->privilegesForm('edit', $valuePrivileges), _('Edit'));
			}
		}
		// new
		$content[] = CmsFactory::response()->fragment()->box()->simpleBox($this->privilegesForm('new', $value), _('Add new'));
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
		$actions = $value['actions'] ?? null;
		$namespace = $value['namespace'] ?? null;

		$form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao form-user-privileges'])
			->action("/admin/user/privileges/$case")->method('post');
		// HIDDEN
		$form->input('iduser', $iduser, 'hidden');
		if ($iduser_privileges) {
			$form->input('iduser_privileges', $iduser_privileges, 'hidden');
		}
		// function
		$form->fieldsetWithSelect('function', $function, self::FUNCTIONS, _('Function') );
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
