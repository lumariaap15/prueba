<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

route::get ('/', ['as' => 'home', 'uses' => 'ArticlesController@index']);
Route::resource('articles','ArticlesController');
Route::resource('movimientos','MovimientosController');


Route::post('getvalorunitario','MovimientosController@getValor')->name('getValor');
Route::post('getarticlesventa','MovimientosController@getArticlesVenta')->name('getArticlesVenta');
Route::post('getarticlescompra','MovimientosController@getArticlesCompra')->name('getArticlesCompra');
Route::post('getdetallesventa','MovimientosController@getDetallesVenta')->name('getDetallesVenta');

Route::post('getsubcategoria','ArticlesController@getCategoria')->name('getCategoria');
Route::post('getarticulos','ArticlesController@getArticulos')->name('getArticulos');
Route::get('categorias','ArticlesController@categorias')->name('articles.categorias');
Route::post('agregarCategoria','ArticlesController@storeCategoria')->name('storeCategoria');
Route::post('agregarSubcategoria','ArticlesController@storeSubcategoria')->name('storeSubcategoria');