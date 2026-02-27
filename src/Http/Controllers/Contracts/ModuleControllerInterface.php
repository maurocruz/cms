<?php
namespace Plinct\Cms\Http\Controllers\Contracts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ModuleControllerInterface
{
	public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
	public function new(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
	public function edit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
