<?php
namespace Plinct\Cms\Http\View\Auth;

use Plinct\Cms\Http\View\Abstracts\ComponentAbstract;

class ChangePasswordView extends ComponentAbstract
{

	public function index(array $data = null): void
	{
		$selector =	$this->getQuerystring('selector');
		$validator = $this->getQuerystring('validator');
		// FORM
		$form = $this->form('changePassword',['class'=>'form formPadrao form-changePassword']);
		$form->action('/admin/auth/change-password')->method('post');
		$form->content("<h3>"._('Change your password')."</h3>");
		// HIDDEN
		$form->input('selector',$selector,'hidden');
		$form->input('validator',$validator,'hidden');
		// PASSWORD
		$form->fieldsetWithInput('password',null,_('Password'),'password', null, ['placeholder' => _('Enter your new password')]);
		// REPEAT PASSWORD
		$form->fieldsetWithInput('repeatPassword',null,_('Repeat password'),'password', null, ['placeholder' => _('Repeat the new password')]);
		// SUBMIT
		$form->input('submit', _('Send'),'submit');
		$this->addMain($form->ready());

	}
}
