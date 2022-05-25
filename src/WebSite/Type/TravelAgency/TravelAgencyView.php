<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\TravelAgency;

use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;

class TravelAgencyView
{
	/**
	 * @var string
	 */
	private string $idorganization;

	/**
	 * @param $title
	 * @return void
	 */
	private function navbar($title = null)
	{
		WebSite::addHeader(Fragment::navbar()
			->title(_('Travel agencies'))
			->ready()
		);

		if($title) {
			WebSite::addHeader(Fragment::navbar()
				->title($title)
				->newTab("/admin/organization/edit/$this->idorganization", sprintf(_("View %s"), 'organization'))
				->newTab("/admin/trip?provider=$this->idorganization", sprintf(_("View all %s"), _("trips")))
				->newTab("/admin/trip/add?provider=$this->idorganization", sprintf(_("Add new %s"), _("trip")))
				->level(3)
				->ready()
			);
		}

	}

  public function index(array $data)
  {
    // NAVBAR
	  $this->navbar();
		// CONTENT
	  WebSite::addMain('<h4>Under development!</h4>');
		// LIST
	}

	/**
	 * @param array $value
	 * @return void
	 */
  public function edit(array $value)
  {
		$this->idorganization = $value['idorganization'];
	  $name =$value['name'];
		// NAVBAR
	  $this->navbar($name);
		// VIEW
	  WebSite::addMain('<h4>Under development!</h4>');
  }
}
