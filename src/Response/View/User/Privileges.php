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

	public function index($data = null): array
	{
		$content = [];
		if ($data) {
			foreach ($data as $value) {
				$content[] = CmsFactory::response()->fragment()->box()->simpleBox($this->privilegesForm('edit', $value), _('Edit'));
			}
		}
		// new
		$content[] = CmsFactory::response()->fragment()->box()->simpleBox($this->privilegesForm(), _('Add new'));
		// return
		return $content;
	}

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
		if ($iduser && $iduser_privileges) {
			$form->input('iduser', $iduser, 'hidden');
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