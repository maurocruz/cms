<?php
namespace Plinct\Cms\Application\Contracts;

use Plinct\Cms\Domain\Auth\User;

interface RequestContextInterface
{
	public function getUser(): ?User;
}
