<?php
namespace Plinct\Cms\Http\View\User;

use Plinct\Cms\Http\View\Abstracts\ModuleViewAbstract;
use Plinct\Cms\Http\View\Template\Template;

class UserlistView extends ModuleViewAbstract
{
	public function __construct(Template $template)
	{
		parent::__construct($template);

		// NAVBAR
		$navbar = $this->navbar()
			->setTitle(_("Users"))
			->newTab("/admin/user",$this->icon()->home())
			->newTab("/admin/user/new",$this->icon()->plus())
			->search();
		$this->addHeader($navbar->ready());

	}

	public function build(array $data = null): void
	{
		//
		$items = $data['data'];
		$orderBy = $data['queryParams']['orderBy'];
		$ordering = $data['queryParams']['ordering'];
		$iconEdit = $this->icon()->edit();

		$this->addMain(['tag'=>'p','content'=>sprintf(_("Showing %s items order by %s %s!"), count($items), $orderBy, $ordering )]);
		$table = $this->table()
			->caption(_("Users"))
			->labels(_("Name"), _('Email'), _('Date modified'))
			->setOrderBy($orderBy)
			->setOrdering($ordering)
			->setProperties(['name','email','dateModified']);

		foreach ($items as $item) {
			$edit = "<a href='/admin/user/edit/{$item['iduser']}'>$iconEdit</a>";
			$table->addRow($edit, $item['name'],$item['email'], $item['dateModified']);
		}
		$table->setEditButton('/admin/user/edit/');

		$this->addMain($table->ready());
	}
}
