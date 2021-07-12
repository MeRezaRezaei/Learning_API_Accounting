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
use App\Http\Middleware\login;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/register','Accounting\user_controller@register');

Route::post('/dologin',[
    'uses' => 'Accounting\user_controller@do_login',
    'as' => 'login_api'
]);

Route::group(['middleware' => login::class ],function(){
    Route::post('/get_user_list',[
        'uses' => 'Accounting\user_controller@get_users_list'
    ]);

    Route::get('/logout','Accounting\user_controller@logout');
});
