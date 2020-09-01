<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/**
 * API VERSION: 1
 */
$API_VERSION = 1;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// routes with proper JSON API Specifications format - without authentication
$router->group(['middleware' => ['json.api.headers'], 'prefix' => 'api/v' . $API_VERSION], function () use ($router) {

    // ************************************************
    // Login and registration
    // ************************************************
    $router->group(['namespace' => 'Users'], function () use ($router) {
        // register
        $router->post('/users/register', [
            'uses' => 'UsersController@register',
            'as' => 'users.register'
        ]);
        // login
        $router->post('/users/login', [
            'uses' => 'UsersController@login',
            'as' => 'users.login'
        ]);
    });
});

// routes with proper JSON API Specifications format - requires authentication
$router->group(['middleware' => ['auth', 'json.api.headers'], 'prefix' => 'api/v' . $API_VERSION], function () use ($router) {

    // ************************************************
    // users
    // ************************************************
    $router->group(['namespace' => 'Users'], function () use ($router) {
        $router->get('/users/{user}', [
            'uses' => 'UsersController@show',
            'as' => 'users.show'
        ]);
        $router->get('/users', [
            'uses' => 'UsersController@index',
            'as' => 'users.index'
        ]);
        $router->patch('/users/{user}', [
            'uses' => 'UsersController@update',
            'as' => 'users.update'
        ]);
        $router->delete('/users/{user}', [
            'uses' => 'UsersController@destroy',
            'as' => 'users.destroy'
        ]);
        // logout
        $router->post('/users/logout', [
            'uses' => 'UsersController@logout',
            'as' => 'users.logout'
        ]);
    });

    // ************************************************
    // sellers
    // ************************************************
    $router->group(['namespace' => 'Sellers'], function () use ($router) {
        $router->get('/sellers/{seller}', [
            'uses' => 'SellersController@show',
            'as' => 'sellers.show'
        ]);
        $router->get('/sellers', [
            'uses' => 'SellersController@index',
            'as' => 'sellers.index'
        ]);
        $router->post('/sellers', [
            'uses' => 'SellersController@store',
            'as' => 'sellers.store'
        ]);
        $router->patch('/sellers/{seller}', [
            'uses' => 'SellersController@update',
            'as' => 'sellers.update'
        ]);
        $router->delete('/sellers/{seller}', [
            'uses' => 'SellersController@destroy',
            'as' => 'sellers.destroy'
        ]);

        // seller-shop relationship 
        $router->get('sellers/{seller}/relationships/shops', [
            'uses' => 'SellersShopsRelationshipsController@index',
            'as' => 'sellers.relationships.shops'
        ]);
        $router->patch('sellers/{seller}/relationships/shops', [
            'uses' => 'SellersShopsRelationshipsController@update',
            'as' => 'sellers.relationships.shops.update'
        ]);
        $router->get('sellers/{seller}/shops', [
            'uses' => 'SellersShopsRelatedController@index',
            'as' => 'sellers.shops'
        ]);
        // seller-user relationship 
        $router->get('sellers/{seller}/relationships/users', [
            'uses' => 'SellersUsersRelationshipsController@index',
            'as' => 'sellers.relationships.users'
        ]);
        $router->patch('sellers/{seller}/relationships/users', [
            'uses' => 'SellersUsersRelationshipsController@update',
            'as' => 'sellers.relationships.users.update'
        ]);
        $router->get('sellers/{seller}/users', [
            'uses' => 'SellersUsersRelatedController@index',
            'as' => 'sellers.users'
        ]);
    });

    // ************************************************
    // shops 
    // ************************************************
    $router->group(['namespace' => 'Shops'], function () use ($router) {
        $router->get('/shops/{shop}', [
            'uses' => 'ShopsController@show',
            'as' => 'shops.show'
        ]);
        $router->get('/shops', [
            'uses' => 'ShopsController@index',
            'as' => 'shops.index'
        ]);
        $router->post('/shops', [
            'uses' => 'ShopsController@store',
            'as' => 'shops.store'
        ]);
        $router->patch('/shops/{shop}', [
            'uses' => 'ShopsController@update',
            'as' => 'shops.update'
        ]);
        $router->delete('/shops/{shop}', [
            'uses' => 'ShopsController@destroy',
            'as' => 'shops.destroy'
        ]);
        // shop-seller relationship 
        $router->get('shops/{shop}/relationships/sellers', [
            'uses' => 'ShopsSellersRelationshipsController@index',
            'as' => 'shops.relationships.sellers'
        ]);
        $router->patch('shops/{shop}/relationships/sellers', [
            'uses' => 'ShopsSellersRelationshipsController@update',
            'as' => 'shops.relationships.sellers.update'
        ]);
        $router->get('shops/{shop}/sellers', [
            'uses' => 'ShopsSellersRelatedController@index',
            'as' => 'shops.sellers'
        ]);
        // shop-shopType relationship 
        $router->get('shops/{shop}/relationships/shop-types', [
            'uses' => 'ShopsShopTypesRelationshipsController@index',
            'as' => 'shops.relationships.shop-types'
        ]);
        $router->patch('shops/{shop}/relationships/shop-types', [
            'uses' => 'ShopsShopTypesRelationshipsController@update',
            'as' => 'shops.relationships.shop-types.update'
        ]);
        $router->get('shops/{shop}/shop-types', [
            'uses' => 'ShopsShopTypesRelatedController@index',
            'as' => 'shops.shop-types'
        ]);
    });

    // *************************************************
    // shop type 
    // *************************************************
    $router->group(['namespace' => 'Shops'], function () use ($router) {
        $router->get('/shop-types/{shopType}', [
            'uses' => 'ShopTypesController@show',
            'as' => 'shop-types.show'
        ]);
        $router->get('/shop-types', [
            'uses' => 'ShopTypesController@index',
            'as' => 'shop-types.index'
        ]);
        $router->post('/shop-types', [
            'uses' => 'ShopTypesController@store',
            'as' => 'shop-types.store'
        ]);
        $router->patch('/shop-types/{shopType}', [
            'uses' => 'ShopTypesController@update',
            'as' => 'shop-types.update'
        ]);
        $router->delete('/shop-types/{shopType}', [
            'uses' => 'ShopTypesController@destroy',
            'as' => 'shop-types.destroy'
        ]);
        // shopType-shop relationship 
        $router->get('shop-types/{shopType}/relationships/shops', [
            'uses' => 'ShopTypesShopsRelationshipsController@index',
            'as' => 'shop-types.relationships.shops'
        ]);
        $router->patch('shop-types/{shopType}/relationships/shops', [
            'uses' => 'ShopTypesShopsRelationshipsController@update',
            'as' => 'shop-types.relationships.shops.update'
        ]);
        $router->get('shop-types/{shopType}/shops', [
            'uses' => 'ShopTypesShopsRelatedController@index',
            'as' => 'shop-types.shops'
        ]);
    });

    // *************************************************
    // brand 
    // *************************************************
    $router->group(['namespace' => 'Brands'], function () use ($router) {
        $router->get('/brands/{brand}', [
            'uses' => 'BrandsController@show',
            'as' => 'brands.show'
        ]);
        $router->get('/brands', [
            'uses' => 'BrandsController@index',
            'as' => 'brands.index'
        ]);
        $router->post('/brands', [
            'uses' => 'BrandsController@store',
            'as' => 'brands.store'
        ]);
        $router->patch('/brands/{brand}', [
            'uses' => 'BrandsController@update',
            'as' => 'brands.update'
        ]);
        $router->delete('/brands/{brand}', [
            'uses' => 'BrandsController@destroy',
            'as' => 'brands.destroy'
        ]);
    });


    // *************************************************
    // product unit
    // *************************************************
    $router->group(['namespace' => 'Products'], function () use ($router) {
        $router->get('/product-units/{productUnit}', [
            'uses' => 'ProductUnitsController@show',
            'as' => 'product-units.show'
        ]);
        $router->get('/product-units', [
            'uses' => 'ProductUnitsController@index',
            'as' => 'product-units.index'
        ]);
        $router->post('/product-units', [
            'uses' => 'ProductUnitsController@store',
            'as' => 'product-units.store'
        ]);
        $router->patch('/product-units/{productUnit}', [
            'uses' => 'ProductUnitsController@update',
            'as' => 'product-units.update'
        ]);
        $router->delete('/product-units/{productUnit}', [
            'uses' => 'ProductUnitsController@destroy',
            'as' => 'product-units.destroy'
        ]);
    });
});
