<?php
namespace Plinct\Cms\Application\Contracts;

use Plinct\Cms\Domain\Auth\Userlogged;

interface RequestContextInterface
{
	public function getUser(): ?Userlogged;
}
