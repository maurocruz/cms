<?php
namespace Plinct\Cms\Http\View\User;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Http\View\Template\Template;
use Plinct\Cms\Http\View\User\Component\UserPrivilegesComponentView;

class UserShowView extends ModuleViewAbstract
{
	public function __construct(Template $template, private readonly UserPrivilegesComponentView $usePrivilegesComponentView)
	{
		parent::__construct($template);
	}

	public function build(array $data = null): void
	{
		$item = $data['data'];
		if (empty($item)) {
			$this->warning(_('User not found'));
		} else {
			$name = $item['name'];
			// NAVBAR
			$this->navbar($name, 3);
			// FROM USER
			$this->formUser($item);
			// PRIVILEGES
			$this->addMain(
				$this->usePrivilegesComponentView->build($this->getContext(), $item['privileges'])
			);
		}
	}

	private function formUser(array $item): void
	{
		$iduser = $item['iduser'];
		$name = $item['name'];
		$email = $item['email'];
		$dateCreated = $item['dateCreated'];
		$dateModified = $item['dateModified'];
		$form = $this->form("form-user",['class'=>'box form-basic form-user']);
		$form->action("/admin/user/update")->method('post');
		$form->content("<h3>"._('Edit User')."</h3>");
		// ID
		$form->input('iduser', $iduser, 'hidden');
		$form->fieldsetWithInput('iduser', $iduser, 'ID', 'text', null, ['disabled']);
		// name
		$form->fieldsetWithInput('name', $name, _('Name'));
		// email
		$form->fieldsetWithInput('email', $email, _("Email"));
		// created date
		$form->fieldsetWithInput('dateCreated', $dateCreated, _("Date created"), 'text', null, ['disabled']);
		// date modified
		$form->fieldsetWithInput('dateModified', $dateModified, _("Date modified"), 'text', null, ['disabled']);
		// submit buttons
		$form->submitButtonSend();
		$form->submitButtonDelete('/admin/user/erase');
		// ready
		$this->addMain($form->ready());
	}
}
