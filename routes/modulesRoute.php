<?php

use Plinct\Cms\Http\Controllers\Modules\ActionController;
use Plinct\Cms\Http\Controllers\Modules\ModuleCreateController;
use Plinct\Cms\Http\Controllers\Modules\ProductController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	// ACTION
	$route->get('/action', [ActionController::class,'list'])->setName('action.modules.list');
	$route->get('/action/edit/{id}', [ActionController::class,'show'])->setName('action.modules.show');
	// PRODUCT
	$route->get('/product', [ProductController::class,'index'])->setName('product.modules.index');
	$route->get('/product/edit/{id}', [ProductController::class,'edit'])->setName('product.modules.edit');
	$route->get('/product/new', [ProductController::class,'new'])->setName('product.modules.new');
	// MODULES
	$route->post('/{type}/new', ModuleCreateController::class)->setName('module.create');

};