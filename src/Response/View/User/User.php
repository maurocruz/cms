<?php
declare(strict_types=1);
namespace Plinct\Cms\Response\View\User;

use Plinct\Cms\CmsFactory;

class User
{
	/**
	 * @param string|null $title
	 * @return void
	 */
	public function navbarUser(string $title = null)
	{
		CmsFactory::webSite()->addHeader(
			CmsFactory::response()->fragment()->navbar()
				->type('user')
				->title(_("Users"))
				->newTab("/admin/user",CmsFactory::response()->fragment()->icon()->home())
				->newTab("/admin/user/new", CmsFactory::response()->fragment()->icon()->plus())
				->level(2)
				->search('/admin/user')
				->ready()
		);
		if ($title) {
			CmsFactory::webSite()->addHeader(
				CmsFactory::response()->fragment()->navbar()
					->title($title)
					->level(3)
					->ready()
			);
		}
	}
	/**
	 * @param array $data
	 * @param string $orderBy
	 * @param string $ordering
	 * @return void
	 */
	public function index(array $data, string $orderBy, string $ordering)
	{
		// navbar
		$this->navbarUser();

		// showing
		CmsFactory::webSite()->addMain(['tag'=>'p','content'=>sprintf(_("Showing %s items order by %s %s!"), count($data), $orderBy, $ordering )]);

		$list = CmsFactory::response()->fragment()->listTable()
			->caption(_("Users"))
			->labels(_("Name"), _('Email'), _('Date modified'))
			->setOrderBy($orderBy)
			->setOrdering($ordering)
			->setProperties(['name','email','dateModified']);

		foreach ($data as $item) {
			$edit = "<a href='/admin/user/edit/{$item['iduser']}'>" . CmsFactory::response()->fragment()->icon()->edit() . "</a>";
			$list->addRow($edit, $item['name'],$item['email'], $item['dateModified']);
		}
		$list->setEditButton('/admin/user/edit/');

		CmsFactory::webSite()->addMain($list->ready());
	}
	/**
	 */
	public function new($params = null)
	{
		$this->navbarUser(_("Add new"));
		CmsFactory::webSite()->addMain(
			CmsFactory::response()->fragment()->auth()->register()
		);
	}
	/**
	 * @param ?array $data
	 */
	public function edit(array $data = null)
	{
		if($data) {
			$value = $data[0];
			$this->navbarUser($value['name']);
			// FORM USER
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->simpleBox(self::formUser("edit", $value), _("Edit user"))
			);
			// PRIVILEGES
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->box()->expandingBox(_('Privileges'),
					$this->privileges()->getPrivileges($value)
				)
			);
		} else {
			$this->navbarUser();
			CmsFactory::response()->message()->noContent();
		}
	}
	/**
	 * @param string $case
	 * @param null $value
	 * @return array
	 */
	static private function formUser(string $case = 'new', $value = null): array
	{
		$id = isset($value) ? $value['iduser'] : null;
		$form = CmsFactory::response()->fragment()->form(['class'=>'formPadrao form-user']);
		$form->action("/admin/user/$case")->method('post');
		// ID
		if ($case == "edit") $form->fieldsetWithInput('iduser',(string) $id, 'ID', 'text', null, ['readonly']);
		// name
		$form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
		// email
		$form->fieldsetWithInput('email', $value['email'] ?? null, _("Email"));
		// password
		if ($case == 'new') $form->fieldsetWithInput('password', $value['password'] ?? null, _("Password"), 'password');
		// created date
		if ($case == 'edit') $form->fieldsetWithInput('dateCreated', $value['dateCreated'], _("Date Created"), 'text', null, ['readonly']);
		// date modified
		if ($case == 'edit') $form->fieldsetWithInput('dateModified', $value['dateModified'], _("Date modified"), 'text', null, ['readonly']);
		// submit buttons
		$form->submitButtonSend();
		if ($case == 'edit') $form->submitButtonDelete('/admin/user/erase');
		// ready
		return $form->ready();
	}
	/**
	 * @return Privileges
	 */
	public function privileges(): Privileges
	{
		return new Privileges();
	}
}
