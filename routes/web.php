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




Route::get('/', 'LandingController@home');
Route::get('blog', 'LandingController@index')->name('blog');
Route::get('blog/detail/{id}', 'LandingController@detail')->name('blog.detail');



Route::group(['middleware' => ['writer']], function () {
    Route::get('/writer/blog','WriterController@index')->name('writer.blog');
    Route::get('/writer/blog/create','WriterController@create')->name('writer.blog.create');
    Route::post('/writer/blog/store','WriterController@store')->name('writer.blog.store');
    Route::get('/writer/show/{id}', 'WriterController@show')->name('writer.blog.show');
    Route::get('/writer/edit/{id}', 'WriterController@edit')->name('writer.blog.edit');
    Route::put('/writer/update/{id}', 'WriterController@update')->name('writer.blog.update');
    Route::get('/writer/destroy/{id}', 'WriterController@destroy')->name('writer.blog.destroy');
});


Route::group(['namespace' => 'Auth'], function () {

    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout')->name('logout');

    Route::get('blog/comment/{id}', 'LandingController@addComment')->name('blog.comment');
    // Registration Routes...
    if (config('auth.users.registration')) {
        Route::get('register', 'RegisterController@showRegistrationForm')->name('register');
        Route::post('register', 'RegisterController@register');
    }

    // Password Reset Routes...
    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'ResetPasswordController@reset');

    // Confirmation Routes...
    if (config('auth.users.confirm_email')) {
        Route::get('confirm/{user_by_code}', 'ConfirmController@confirm')->name('confirm');
        Route::get('confirm/resend/{user_by_email}', 'ConfirmController@sendEmail')->name('confirm.send');
    }

    // Social Authentication Routes...
    Route::get('social/redirect/{provider}', 'SocialLoginController@redirect')->name('social.redirect');
    Route::get('social/login/{provider}', 'SocialLoginController@login')->name('social.login');
});

/**
 * Backend routes
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => 'admin'], function () {

    // Dashboard
    Route::get('/', 'DashboardController@index')->name('dashboard');

    // Blog

    Route::get('blog','BlogController@index')->name('blog');
    Route::get('users/create', 'BlogController@create')->name('blog.create');
    Route::post('users/store/', 'BlogController@store')->name('blog.store');
    Route::get('users/show/{id}', 'BlogController@show')->name('blog.show');
    Route::get('users/edit/{id}', 'BlogController@edit')->name('blog.edit');
    Route::put('users/update/{id}', 'BlogController@update')->name('blog.update');
    Route::get('users/destroy/{id}', 'BlogController@destroy')->name('blog.destroy');


    //Users
    Route::get('users', 'UserController@index')->name('users');
    Route::get('users/restore', 'UserController@restore')->name('users.restore');
    Route::get('users/{id}/restore', 'UserController@restoreUser')->name('users.restore-user');
    Route::get('users/{user}', 'UserController@show')->name('users.show');
    Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
    Route::put('users/{user}', 'UserController@update')->name('users.update');
    Route::any('users/{id}/destroy', 'UserController@destroy')->name('users.destroy');
    Route::get('permissions', 'PermissionController@index')->name('permissions');
    Route::get('permissions/{user}/repeat', 'PermissionController@repeat')->name('permissions.repeat');
    Route::get('dashboard/log-chart', 'DashboardController@getLogChartData')->name('dashboard.log.chart');
    Route::get('dashboard/registration-chart', 'DashboardController@getRegistrationChartData')->name('dashboard.registration.chart');
    Route::get('dashboard/reporting', 'DashboardController@getReporting')->name('dashboard.reporting');
    Route::get('dashboard/allProduct', 'DashboardController@getAllProduct')->name('dashboard.allProduct');
    Route::get('dashboard/chartProduct', 'DashboardController@getChartProduct')->name('dashboard.chartProduct');
});


/**
 * Membership
 */
Route::group(['as' => 'protection.'], function () {
    Route::get('membership', 'MembershipController@index')->name('membership')->middleware('protection:' . config('protection.membership.product_module_number') . ',protection.membership.failed');
    Route::get('membership/access-denied', 'MembershipController@failed')->name('membership.failed');
    Route::get('membership/clear-cache/', 'MembershipController@clearValidationCache')->name('membership.clear_validation_cache');
});


Route::post('/2fa',function (){
    return redirect(URL()->previous());
})->name('2fa')->middleware('2fa');

Route::get('tes/2fa','MembershipController@index')->name('tes.2fa')->middleware(['auth','2fa']);





