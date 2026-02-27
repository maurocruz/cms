<?php
namespace Plinct\Cms\Http\Controllers\Auth;

use GuzzleHttp\Exception\GuzzleException;
use Plinct\Cms\Application\Context\RequestContext;
use Plinct\Cms\Application\User\UserUseCase;
use Plinct\Cms\Http\View\User\UserlistView;
use Plinct\Cms\Http\View\User\UserShowView;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class UserController
{

	public function __construct(private UserUseCase $userUseCase, private UserlistView $list, private UserShowView $show)
	{
	}

	/**
	 * @throws GuzzleException
	 */
	public function list(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		// CONTEXT
		$context = $request->getAttribute(RequestContext::class);
		// PARSE QUERYSTRINGS
		$defaultQueryParams = ['orderBy'=>'dateModified','ordering'=>'DESC'];
		$queryParams = array_merge($defaultQueryParams, $request->getQueryParams());
		// GET USE CASE (Application)
		$apiData = $this->userUseCase->list($queryParams);
		// SEND VIEW (Http)
		$this->list->setContext($context);
		$this->list->index($apiData);
		// WRITE RESPONSE
		$response->getBody()->write($this->list->render());
		return $response;
	}

	/**
	 * @throws GuzzleException
	 */
	public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$id = $request->getAttribute('id');
		$queryParams = $request->getQueryParams();
		// CONTEXT
		$context = $request->getAttribute(RequestContext::class);
		// GET USE CASE (Application)
		$apiData = $this->userUseCase->show($id);
		// SEND VIEW (Http)
		$this->show->setContext($context);
		$this->show->index($apiData);
		// WRITE RESPONSE
		$response->getBody()->write($this->show->render());
		return $response;
	}
}
