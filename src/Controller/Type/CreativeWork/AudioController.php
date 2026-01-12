<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

class AudioController extends MediaObjectController
{
	public function __construct(string $type = 'audioObject')
	{
		parent::__construct($type);
	}
}
