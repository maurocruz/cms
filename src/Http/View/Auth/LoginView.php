<?php
namespace Plinct\Cms\Http\View\Auth;

use Plinct\Cms\Http\View\Abstracts\ViewAbstract;

class LoginView extends ViewAbstract
{
	/**
	 * @param array|null $params
	 * @return void
	 */
	public function build(array $params = null): void
	{
		$email = $params['email'] ?? null;
		$form = $this->component()->form('form-login', ['class'=>'form formPadrao form-login']);
		$form->action('/admin/auth/login')->method('post');
		$form->addMandatories(['email','password']);
		$form->content("<h3>"._('Log in')."</h3>");
		// EMAIL
		$form->fieldsetWithInput('email', $email, 'Email');
		// PASSWORD
		$form->fieldsetWithInput('password', null, _('Password'),'password');
		// SUBMIT
		$form->input('submit', _('Send'),'submit');
		$form->content("<div class='form-login-actions'>
        <p><a href='/admin/auth/register'>"._("Sign on")."</a></p>
        <p><a href='/admin/auth/reset-password'>"._("Forgot password?")."</a></p>
      </div>");
		$this->addMain($form->ready());

	}
}
