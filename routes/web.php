<?php

Route::get('/', 'PagesController@index')->name('home');

Route::get('/myBookings', 'PagesController@bookings');
Route::get('/login', 'PagesController@authentication')->name('login');
Route::get('/building/{building}', 'BuildingsController@show');
Route::get('/building/floors/{building}', 'BuildingsController@fetch');


/*
 * Razvan
 */
Route::get('/user', 'UsersController@fetch');
Route::post('/book', 'BookingsController@store');
Route::patch('/book/{booking}', 'BookingsController@update');
Route::delete('/book/{booking}', 'BookingsController@delete');
Route::post('/invitation/{booking}', 'MailsController@invitation');
Route::post('/confirm/{booking}', 'MailsController@confirm');

Route::get('/show/invitation/{booking}', 'MailsController@show');
// Route::get('/test' , "PagesController@test");






/*
 * Radu
 */
Route::post('/login', 'SessionController@login');
Route::post('/register', 'RegistrationController@store');
Route::get('/admin', 'AdminControllers\AdminController@index');
Route::get('/logout', 'AdminControllers\AdminController@destroy');
Route::get('/admin/history', 'AdminControllers\HistoryController@show');
Route::get('/admin/history/fetch', 'AdminControllers\HistoryController@fetch');
Route::post('/admin/history/update', 'AdminControllers\HistoryController@update');
Route::get('/admin/fetch', 'AdminControllers\BuildingsController@fetch');
Route::post('/admin/store', 'AdminControllers\BuildingsController@store');
Route::post('/admin/building/update', 'AdminControllers\BuildingsController@update');
Route::post('/admin/building/delete', 'AdminControllers\BuildingsController@delete');
Route::post('/admin/floors/store', 'AdminControllers\FloorController@store');
Route::post('/admin/floor/update', 'AdminControllers\FloorController@update');
Route::post('/admin/floor/delete', 'AdminControllers\FloorController@delete');
Route::post('/admin/rooms/store', 'AdminControllers\RoomController@store');
Route::post('/admin/room/update', 'AdminControllers\RoomController@update');
Route::post('/admin/room/delete', "AdminControllers\RoomController@delete");