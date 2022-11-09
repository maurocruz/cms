<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Section\User;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;

class UserView
{
  /**
   * @param string|null $title
   * @return void
   */
  public function navbarUser(string $title = null)
  {
    WebSite::addHeader(Fragment::navbar()
      ->type('user')
      ->title(_("Users"))
      ->newTab("/admin/user",Fragment::icon()->home())
      ->newTab("/admin/user/new", Fragment::icon()->plus())
      ->level(2)
      ->search('/admin/user')
      ->ready()
    );

    if ($title) {
      WebSite::addHeader(Fragment::navbar()
        ->title($title)
        ->level(3)
        ->ready()
      );
    }
  }

  /**
   * @param array $data
   * @return void
   */
  public function indexView(array $data)
  {
    // navbar
    $this->navbarUser();
    // showing
    WebSite::addMain([ "tag" => "p", "content" => sprintf(_("Showing %s items!"), count($data) )]);

    $list = Fragment::listTable();
    $list->caption(_("Users"));
    $list->labels(_("Name"), _('Email'), 'Status');
		foreach ($data as $item) {
			$edit = "<a href='/admin/user/edit/{$item['iduser']}'>" . Fragment::icon()->edit() . "</a>";
			$list->addRow($edit, $item['name'],$item['email'], $item['status']);
		}
    //$list->rows($data,['name','email','status']);
    $list->setEditButton('/admin/user/edit/');

    WebSite::addMain($list->ready());
  }

  /**
   */
  public function newView($params = null)
  {
    $status = filter_input(INPUT_GET,'status');
    $message = filter_input(INPUT_GET,'message');

		$this->navbarUser(_("Add new"));

    if ($status == 'fail') {
      WebSite::addMain("<p class='aviso'>" . _($message) . "</p>");
    }

    WebSite::addMain(Fragment::box()->simpleBox(self::formUser(),_("Add new")));
  }

  /**
   * @param array $data
   */
  public function editView(array $data)
  {
    $this->navbarUser($data['name']);

    WebSite::addMain(Fragment::box()->simpleBox(self::formUser("edit", $data), _("Edit user"),['class'=>'box','style'=>'max-width: 850px; margin: 0 auto;']));
  }

  /**
   * @param string $case
   * @param null $value
   * @return array
   */
  static private function formUser(string $case = 'new', $value = null): array
  {
    $id = isset($value) ? $value['iduser'] : null;
    $form = Fragment::form([ "class" => "formPadrao form-user" ]);
    $form->action("/admin/user/$case")->method('post');
    // ID
    if ($case == "edit") $form->fieldsetWithInput('iduser', $id, 'ID', 'text', null, ['readonly']);
    // name
    $form->fieldsetWithInput('name', $value['name'] ?? null, _('Name'));
    // email
    $form->fieldsetWithInput('email', $value['email'] ?? null, _("Email"));
    // password
    if ($case == 'new') $form->fieldsetWithInput('password', $value['password'] ?? null, _("Password"), 'password');
    // status
    $statusText = _(UserController::getStatusWithText($value['status'] ?? null));
    $form->fieldsetWithSelect('status', [$value['status'] ?? '0' => $statusText ], ['0'=>_('User'),'1'=>_('Administrator')], _('Status'));
    // created date
    if ($case == 'edit') $form->fieldsetWithInput('dateCreated', $value['dateCreated'], _("Date Created"), 'text', null, ['readonly']);
    // submit buttons
    $form->submitButtonSend();
    if ($case == 'edit') $form->submitButtonDelete('/admin/user/erase');
    // ready
    return $form->ready();
  }
}
