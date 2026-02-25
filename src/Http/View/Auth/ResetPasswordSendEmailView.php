<?php
namespace Plinct\Cms\Http\View\Auth;

use Plinct\Cms\Http\View\Abstracts\TemplateViewAbstract;

class ResetPasswordSendEmailView extends TemplateViewAbstract
{
	public function build(array $data = null): void
	{
		$form = $this->form('resetPassword',['class'=>'form formPadrao form-resetPassword']);
		$form->action('/admin/auth/reset-password')->method('post');
		$form->addMandatories(['email']);
		// TITLE
		$form->content("<h3>"._('Reset password')."</h3>");
		// EMAIL
		$form->fieldsetWithInput('email', null, _('Email'), 'email');
		// SUBMIT
		$form->input('submit', _('Send'),'submit');
		$this->addMain($form->ready());
	}
}
