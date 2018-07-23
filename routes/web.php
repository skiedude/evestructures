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

Auth::routes();

Route::get('/', 'WelcomeController@index');
Route::get('/demo', 'DemoController@index')->name('demo');
Route::get('/demo/structure/{structure_id}', 'DemoController@show');

Route::get('/sso/login', function() {
    $authsite = 'https://login.eveonline.com/oauth/authorize';
    $client_id = env('CLIENT_ID');
    $redirect_uri = env('CALLBACK_URL');
    $scopes = "esi-corporations.read_structures.v1 esi-characters.read_corporation_roles.v1 esi-universe.read_structures.v1 esi-industry.read_corporation_mining.v1";
    $state = uniqid();
    session(['auth_state' => $state]);

    return redirect($authsite . '?response_type=code&redirect_uri=' . $redirect_uri
           . '&client_id=' . $client_id . '&scope=' . $scopes . '&state=' . $state);

});
 
//SSO RELATED//
Route::get('/sso/callback', 'CharacterController@create');
Route::get('/fetch/{character_id}', 'StructureController@create');

//HOME ROUTES//
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home/notifications', 'NotificationManagerController@index')->name('notifications');
Route::get('/home/structure/{structure_id}', 'StructureController@show');
Route::post('/home/structure/{structure_id}', 'StructureController@updateExtraction');

//DELETE ROUTES//
Route::get('/delete/{character_id}', 'CharacterController@destroy');
Route::get('/account/delete', 'HomeController@deleteAccount');

//WEBHOOK ROUTES//
Route::post('/webhook/{character_id}', 'WebhookController@store');
Route::delete('/webhook/delete/{character_id}', 'WebhookController@destroy');
Route::post('/webhook/test/{character_id}', 'WebhookController@testNotify');

//PUBLIC EXTRACTIONS//
Route::get('/extraction/', 'SlugController@index')->name('extraction');
Route::get('/extraction/{corporation_id}/{slug}', 'PublicExtraction@index');
Route::post('/extraction/create/{character_id}', 'SlugController@create');
Route::delete('/extraction/delete/{character_id}', 'SlugController@destroy');
