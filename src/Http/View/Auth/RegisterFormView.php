<?php
namespace Plinct\Cms\Http\View\Auth;

use Plinct\Cms\Http\View\Abstracts\ComponentAbstract;

class RegisterFormView extends ComponentAbstract
{

	public function index(array $data = null): void
	{
		$name = $data['name'] ?? null;
		$email = $data['email'] ?? null;
		$form = $this->form('form-register',['class'=>'form formPadrao form-register']);
		$form->action('/admin/auth/register')->method('post');
		$form->addMandatories(['name','email','password','passwordRepeat']);
		$form->content("<h3>"._('Sign on')."</h3>");
		// NAME
		$form->fieldsetWithInput('name', $name, _('Name'));
		// EMAIL
		$form->fieldsetWithInput('email', $email, 'Email', 'email');
		// PASSWORD
		$form->fieldsetWithInput('password', null, _('Password'),'password');
		// REPEAT PASSWORD
		$form->fieldsetWithInput('passwordRepeat', null, _('Repeat password'),'password');
		// SUBMIT
		$form->input('submit', _('Send'),'submit');
		// RESPONSE
		$this->addMain($form->ready());
	}
}
