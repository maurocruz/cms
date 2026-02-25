<?php

use Plinct\Cms\Http\Controllers\Auth\ChangePasswordController;
use Plinct\Cms\Http\Controllers\Auth\ChangePasswordFormController;
use Plinct\Cms\Http\Controllers\Auth\LoginAuthehticationController;
use Plinct\Cms\Http\Controllers\Auth\LoginFormController;
use Plinct\Cms\Http\Controllers\Auth\LogoutController;
use Plinct\Cms\Http\Controllers\Auth\RegisterFormController;
use Plinct\Cms\Http\Controllers\Auth\RegisterSendController;
use Plinct\Cms\Http\Controllers\Auth\ResetPasswordFormEmailController;
use Plinct\Cms\Http\Controllers\Auth\ResetPasswordSendEmailController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('/auth', function (RouteCollectorProxy $route) {
		// LOGOUT
		$route->get('/logout', LogoutController::class)->setName('auth.logout.redir');

		// LOGIN
		$route->get('/login', LoginFormController::class)->setName('auth.login.read');
		$route->post('/login', LoginAuthehticationController::class)->setName('auth.login.send');

		// REGISTER
		$route->get('/register', RegisterFormController::class)->setName('auth.register.read');
		$route->post('/register', RegisterSendController::class)->setName('auth.register.send');

		// RESET PASSWORD
		$route->get('/reset-password', ResetPasswordFormEmailController::class)->setName('auth.resetPassword.read');
		$route->post('/reset-password', ResetPasswordSendEmailController::class)->setName('auth.resetPassword.send');

		// CHANGE PASSWORD
		$route->get('/change-password', ChangePasswordFormController::class)->setName('auth.changePassword.read');
		$route->post('/change-password', ChangePasswordController::class)->setName('auth.changePassword.send');

	});
};