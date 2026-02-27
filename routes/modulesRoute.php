<?php

use Plinct\Cms\Http\Controllers\Modules\ActionController;
use Plinct\Cms\Http\Controllers\Modules\ModuleCreateController;
use Plinct\Cms\Http\Controllers\Modules\ModuleUpdateController;
use Plinct\Cms\Http\Controllers\Modules\ProductController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	// ACTION
	$route->get('/action', [ActionController::class,'index'])->setName('action.modules.index');
	$route->get('/action/edit/{id}', [ActionController::class,'edit'])->setName('action.modules.edit');
	$route->get('/action/new', [ActionController::class,'new'])->setName('action.modules.new');
	// PRODUCT
	$route->get('/product', [ProductController::class,'index'])->setName('product.modules.index');
	$route->get('/product/edit/{id}', [ProductController::class,'edit'])->setName('product.modules.edit');
	$route->get('/product/new', [ProductController::class,'new'])->setName('product.modules.new');
	// MODULES
	$route->post('/{type}/create', ModuleCreateController::class)->setName('module.create');
	$route->post('/{type}/update', ModuleUpdateController::class)->setName('module.update');
};