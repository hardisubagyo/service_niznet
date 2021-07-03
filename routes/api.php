<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => 'auth:api'], function() {
});


//Route::get('cek', function(){
//   dd('aa');
// });
Route::post('login', 'API\UserController@login');
Route::post('login-kurir', 'API\UserController@loginKurir');
Route::post('register', 'API\UserController@register');
Route::post('cek-email', 'API\UserController@checkEmail');
 
Route::group(['middleware' => 'auth:api'], function(){
  Route::post('update-profile', 'API\UserController@updateProfile');
  Route::post('details', 'API\UserController@details');
  
  Route::group(['as' => 'api-transaksi', 'prefix' => 'transaksi'], function() {
    Route::get('/', 'Services\TransaksiController@index');
    Route::get('get-list', ['as' => '-get-list', 'uses' => 'Services\TransaksiController@getList']);
    Route::post('add', ['as' => '-add', 'uses' => 'Services\TransaksiController@add']);
    Route::post('edit', ['as' => '-edit', 'uses' => 'Services\TransaksiController@edit']);
    Route::post('delete', ['as' => '-delete', 'uses' => 'Services\TransaksiController@delete']);
  });

  Route::group(['as' => 'api-produk', 'prefix' => 'produk'], function() {
    Route::post('upload-img', ['as' => '-upload-img', 'uses' => 'API\ProdukController@uploadImg']);
    Route::post('add', ['as' => '-add', 'uses' => 'API\ProdukController@add']);
    Route::post('edit', ['as' => '-edit', 'uses' => 'API\ProdukController@edit']);
    Route::post('delete', ['as' => '-delete', 'uses' => 'API\ProdukController@delete']);
  });

  Route::group(['as' => 'api-Transaction', 'prefix' => 'transaction'], function() {
    Route::get('/', 'API\TransactionController@index');
    Route::get('get-list-cart', ['as' => '-get-list-cart', 'uses' => 'API\TransactionController@getListCart']);
    Route::get('get-list-master-trx', ['as' => '-get-list-master-trx', 'uses' => 'API\TransactionController@getListMasterTrx']);
    Route::get('get-list-master-trx-filter', ['as' => '-get-list-master-trx-filter', 'uses' => 'API\TransactionController@getListMasterTrxFilter']);
    Route::post('add-cart', ['as' => '-add-cart', 'uses' => 'API\TransactionController@addCart']);
    Route::post('checkout', ['as' => '-checkout', 'uses' => 'API\TransactionController@checkout']);
    Route::post('edit', ['as' => '-edit', 'uses' => 'API\TransactionController@edit']);
    Route::post('delete', ['as' => '-delete', 'uses' => 'API\TransactionController@delete']);
  });

  Route::group(['as' => 'api-tracking', 'prefix' => 'tracking'], function() {
    Route::get('/', 'API\TrackingController@index');
    Route::get('get-list', ['as' => '-get-list', 'uses' => 'API\TrackingController@getList']);
    Route::get('get-detail-list', ['as' => '-get-detail-list', 'uses' => 'API\TrackingController@getListDetail']);
    Route::post('upload-img', ['as' => '-upload-img', 'uses' => 'API\TrackingController@uploadImg']);
    Route::post('update-status', ['as' => '-update-status', 'uses' => 'API\TrackingController@updateStatus']);
  });


});


Route::get('get-toko-list', ['as' => '-get-toko-list', 'uses' => 'API\TransactionController@listToko']);
Route::get('get-payment-method', ['as' => '-get-payment-method', 'uses' => 'API\TransactionController@getPaymentMethod']);
Route::get('get-bank-list', ['as' => '-get-bank-list', 'uses' => 'API\TransactionController@GetBankList']);

Route::group(['as' => 'api-produk', 'prefix' => 'produk'], function() {
  Route::get('/', 'API\ProdukController@index');
  Route::get('get-list', ['as' => '-get-list', 'uses' => 'API\ProdukController@getList']);
  Route::get('get-list-new-product', ['as' => '-get-list-new-product', 'uses' => 'API\ProdukController@getNewProduct']);
  Route::get('get-list-kategori', ['as' => '-get-list-kategori', 'uses' => 'API\ProdukController@getListKategori']);
  Route::get('get-list-brand', ['as' => '-get-list-brand', 'uses' => 'API\ProdukController@getListBrand']);
  Route::get('show-img', ['as' => '-show-img', 'uses' => 'API\ProdukController@showImg']);
});
