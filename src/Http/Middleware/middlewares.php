<?php

use Plinct\Cms\Http\ExceptionHandlers\HttpUnauthorizedHandler;
use Plinct\Cms\Http\ExceptionHandlers\HttpForbiddenHandler;
use Plinct\Cms\Http\ExceptionHandlers\HttpNotFoundHandler;
use Plinct\Cms\Http\Middleware\AuthenticationMiddleware;
use Plinct\Cms\Http\Middleware\RemoteProcedureCallMiddleware;
use Slim\App as SlimApp;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;

return function (SlimApp $slimApp, bool $debug = false)
{
	// PHP ERRORS
	error_reporting($debug ? E_ALL : 0);
	// MIDDLEWARES
	$slimApp->addRoutingMiddleware();
	// ERROR HANDLER
	$errorMiddleware = $slimApp->addErrorMiddleware($debug, $debug, $debug);

	// AUTHENTICATION MIDDLEWARE
	$slimApp->add(AuthenticationMiddleware::class);
	// RPC MIDDLEWARE
	$slimApp->add(RemoteProcedureCallMiddleware::class);

	// DEFINE CUSTOM ERROR HANDLER FOR 404
	$errorMiddleware->setErrorHandler(HttpNotFoundException::class, HttpNotFoundHandler::class);
	// UNAUTHORIZED
	$errorMiddleware->setErrorHandler(HttpUnauthorizedException::class, HttpUnauthorizedHandler::class);
	// FORBIDDEN HANDLER
	$errorMiddleware->setErrorHandler(HttpForbiddenException::class, HttpForbiddenHandler::class);

};
