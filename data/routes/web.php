<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'App\Http\Controllers\WelcomeController@index')->name('welcome');
Route::group(['prefix' => 'content', 'as' => 'content.'], function () {
    Route::get('{pageName}', 'App\Http\Controllers\ContentController@index')->name('page');
});

Route::get('sendAuthorize', 'App\Http\Controllers\AuthorizeController@index')->name('sendAuthorize');
Route::get('authRedirect', 'App\Http\Controllers\AuthRedirectController@index');
Route::group(['prefix' => 'page', 'as' => 'page.'], function () {
    Route::get('create', 'App\Http\Controllers\PageController@create')->name('create');
    Route::post('new', 'App\Http\Controllers\PageController@new')->name('new');
    Route::get('list', 'App\Http\Controllers\PageController@list')->name('list');
    Route::get('{pageId}/edit', 'App\Http\Controllers\PageController@edit')->name('edit');
});
