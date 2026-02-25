<?php
namespace Plinct\Cms\View\Authentication;

use Plinct\Cms\CmsFactory;

class AuthenticationView
{
	/**
	 * @param string|null $warning
	 * @return void
	 */
	public function installDatabase(string $warning = null): void
	{
		$warningItem = $warning ? "<p class='warning'>$warning</p>" : null;
		CmsFactory::View()->addMain([
			"<div class='warning'>"
				."<p>"._('Tables do not exist!')."</p>"
				."<p>"._('Iniciar aplicação').". Ao enviar seus dados, uma base de dados será montada e você será admitido como primeiro usuário administrado.</p>"
			."</div>",
			$warningItem,
			$this->formRegister('/admin/config/installDatabase')
		]);
	}

	/**
	 * @param string $action
	 * @return array
	 */
	private function formRegister(string $action = '/admin/auth/register'): array
	{
		$form = CmsFactory::view()->fragment()->form('formRegister', ['class' => 'form form-basic form-register']);
		$form->action($action)->method('post');
		$form->addMandatories(['name', 'email', 'password', 'passwordRepeat']);
		// NAME
		$form->fieldsetWithInput('name', null, _('Name'), 'text', null, ['required']);
		// EMAIL
		$form->fieldsetWithInput('email', null, _("Email"), 'email', null, ['required']);
		// PASSWORD
		$form->fieldsetWithInput('password', null, _("Password"), 'password', null, ['required']);
		// REPEAT PASSWORD
		$form->fieldsetWithInput('passwordRepeat', null, _("Repeat password"), 'password', null, ['required']);
		// SUBMIT
		$form->submitButtonSend();
		return $form->ready();
	}
}
