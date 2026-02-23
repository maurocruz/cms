<?php
namespace Plinct\Cms\Application\Contracts;

interface UseCaseInterface
{
	public function isSuccess(array $responseData): bool;
}
