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

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth:api'], function () {
    // Logout
    Route::post('logout', 'Auth\LoginController@logout');

    // Me
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    // Settings
    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');
    Route::post('settings/updateAvatar', 'Settings\ProfileController@updateAvatar');

    // Notifications
    Route::get('user/notifications', 'NotificationController@index');
    Route::get('user/notifications/{id}', 'NotificationController@show');
    Route::put('user/markNotificationsAsRead', 'NotificationController@markMultipleAsRead');
    Route::put('user/notifications/{id}/markAsRead', 'NotificationController@markAsRead');
    Route::put('user/notifications/{id}/unmarkAsRead', 'NotificationController@unmarkAsRead');
    Route::delete('user/notifications/{id}', 'NotificationController@destroy');

    // Series
    Route::post('series/', 'SerieController@store');
    Route::put('series/{id}', 'SerieController@update');
    Route::post('series/{id}/updateCover', 'SerieController@updateCover');
    Route::post('series/{id}/updateBanner', 'SerieController@updateBanner');
    Route::post('series/{id}/removeBanner', 'SerieController@removeBanner');
    Route::get('user/series', 'SerieController@userIndex');
    Route::get('user/series/{id}', 'SerieController@userShow');

    // Serie Like System
    Route::post('user/like/{serieId}', 'LikeController@like');
    Route::post('user/unlike/{serieId}', 'LikeController@unlike');

    // Serie Subscriber System
    Route::get('user/subscriptions', 'SubscribeController@userIndex');
    Route::post('user/subscribe/{serieId}', 'SubscribeController@subscribe');
    Route::post('user/unsubscribe/{serieId}', 'SubscribeController@unsubscribe');

    // Chapters
    Route::post('chapters/', 'ChapterController@store');
    Route::put('chapters/{id}', 'ChapterController@update');
    Route::get('user/series/{id}/chapters', 'ChapterController@userSerieIndex');
    Route::get('user/chapters/{id}', 'ChapterController@userShow');
    Route::delete('user/chapters/{id}', 'ChapterController@destroy');
    Route::post('chapters/{id}/updateThumbnail', 'ChapterController@updateThumbnail');
    Route::post('chapters/{id}/removeThumbnail', 'ChapterController@removeThumbnail');

    // Pages
    Route::post('pages/uploadImage', 'PageController@uploadImage');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Strictly guest)
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'guest:api'], function () {
    // Auth
    Route::post('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');

    // Forgot Password
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    // Social Auth
    Route::post('oauth/{provider}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{provider}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

// Genres
Route::get('genres', 'GenreController@index');

// Licences
Route::get('licences', 'LicenceController@index');

// Authors
Route::get('authors', 'AuthorController@index');
Route::get('authors/{id}', 'AuthorController@show');

// Series
Route::get('series/', 'SerieController@index');
Route::get('newSeries/', 'SerieController@new');
Route::get('series/{id}', 'SerieController@show');
Route::get('authors/{id}/series', 'SerieController@authorIndex');
Route::get('series/', 'SerieController@index');

// Serie Subscriber System
Route::get('series/{id}/subscribers', 'SubscribeController@serieIndex');

// Chapters
Route::get('series/{id}/chapters/', 'ChapterController@serieIndex');
Route::get('chapters/{id}', 'ChapterController@show');

// Suggestions
Route::post('suggestions', 'SuggestionController@store');
