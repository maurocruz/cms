<?php
namespace Plinct\Cms\Http\Controllers\Abstracts;

use Plinct\Cms\Http\View\Template\Template;

abstract class ControllerAbstract
{
	public function __construct(protected Template $template)
	{
	}
}
