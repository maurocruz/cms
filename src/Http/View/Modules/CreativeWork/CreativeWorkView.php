<?php
namespace Plinct\Cms\Http\View\Modules\CreativeWork;

use Plinct\Cms\Http\View\Modules\Thing\ThingView;

class CreativeWorkView extends ThingView
{

	public function index(array $data = null): void
	{
		// NAVBAR
		$this->addHeader(CreativeWorkComponentView::navbar());
		// CONTENT MAIN
		$this->addMain(
			$this->reactShell('creativeWork')->ready()
		);
	}

	public function new(array $data = null): void
	{
		// NAVBAR
		$this->addHeader(CreativeWorkComponentView::navbar());
		// CONTENT MAIN
		$form = $this->form("form-creativeWork", ['class'=>'form-basic form-creativeWork']);
		$this->addMain(
			$this->box()->expandingBox(_('New creativeWork'), CreativeWorkComponentView::form($form), true)
		);
	}

	public function edit(array $data = null): void
	{
		var_dump($data);
	}
}
