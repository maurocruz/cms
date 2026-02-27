<?php
namespace Plinct\Cms\Http\View\User\Component;

use Plinct\Cms\Application\User\UserCommand;
use Plinct\Cms\Http\View\Abstracts\ComponentAbstract;
use Plinct\Cms\Http\View\Component\ComponentFactory;
use Plinct\Cms\Http\View\Template\Template;

class UserPrivilegesComponentView extends ComponentAbstract
{
	private array $functionOptions = [
		'1'=>'visitor',
		'2'=>'collaborator',
		'3'=>'moderator',
		'4'=>'administrator',
		'5'=>'super'
	];
	private $modulesAvailable;

	public function __construct(ComponentFactory $componentFactory, Template $template, private readonly UserCommand $userCommand)
	{
		parent::__construct($componentFactory, $template);
	}

	public function list(array $data = null): void
	{
		$this->privileges($data);
	}

	public function privileges($context, array $data = null): array
	{
		$user = $context->getUser();
		$this->modulesAvailable = $context->getModulesAvailable();
		$content = [];
		$userLoggedPrivilegesArray = $user->getPrivileges();
		$userLoggedPrivileges = isset($userLoggedPrivilegesArray[0]) ? $userLoggedPrivilegesArray[0]['function'] : 1;
		$functionOptions = array_slice($this->functionOptions, 0, $userLoggedPrivileges, true);
		// TRANSLATE
		foreach ($functionOptions as $key => $item) {
			$this->functionOptions[$key] = _($item);
		}
		// USER PRIVILEGES
		if ($data) {
			foreach ($data as $valuePrivileges) {
				$content[] = $this->box()->simpleBox($this->privilegesForm('edit', $valuePrivileges), _('Edit'));
			}
		}
		// NEW PRIVILEGES
		if ($this->userCommand->comparePrivileges($data,5,'crud','all')) {
			$content[] = $this->box()->simpleBox($this->privilegesForm('new', ['iduser'=>$user->getIduser()]), _('Add new'));
		}
		return $this->box()->expandingBox(_('Privileges'),$content);
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
		$function = $value['function'] ?? null;
		$action = $value['action'] ?? null;
		$namespace = $value['namespace'] ?? null;
		$userCreator = $value['userCreator'] ?? null;
		$form = $this->form("form-privileges",['class'=>'form-basic form-user-privileges'])
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
		$items = [];
		foreach ($this->modulesAvailable as $valueModule) {
			$items[$valueModule] = $valueModule;
		}
		$items['all'] = 'all';
		$valuesChecked = [];
		if (is_string($namespace)) {
			foreach (explode(',', $namespace) as $char) {
				if (in_array($char, $this->modulesAvailable)) {
					$valuesChecked[$char] = $char;
				}
			}
			if ($namespace && empty($valuesChecked)) {
				$valuesChecked[$namespace] = $namespace;
			}
		}
		$form->fieldsetWithCheckbox('namespace', $items, $valuesChecked, _('Namespace'));

		// BUTTONS
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
