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
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    // users
    // Route::get('/admin/users', 'Admin\UserController@index');
    // Route::get('/admin/users/{id}', 'Admin\UserController@show');
    // Route::put('/admin/users/{id}/assignRole', 'Admin\UserController@assignRole');
    // Route::put('/admin/users/{id}/unassignRole', 'Admin\UserController@unassignRole');

    // roles
    // Route::get('/admin/roles', 'Admin\RoleController@index');
    // Route::post('/admin/roles', 'Admin\RoleController@store');
    // Route::get('/admin/roles/{id}', 'Admin\RoleController@show');
    // Route::put('/admin/roles/{id}', 'Admin\RoleController@update');
    // Route::put('/admin/roles/{id}/assignAbility', 'Admin\RoleController@assignAbility');
    // Route::put('/admin/roles/{id}/unassignAbility', 'Admin\RoleController@unassignAbility');
    // Route::get('/admin/searchRoles', 'Admin\RoleController@search');

    // abilities
    // Route::get('/admin/abilities', 'Admin\AbilityController@index');
    // Route::get('/admin/abilities/{id}', 'Admin\AbilityController@show');
    // Route::put('/admin/abilities/{id}', 'Admin\AbilityController@update');
    // Route::get('/admin/searchAbilities', 'Admin\AbilityController@search');

    /*
    |--------------------------------------------------------------------------
    | Non Admin Routes
    |--------------------------------------------------------------------------
    */

    // Logout
    Route::post('logout', 'Auth\LoginController@logout');

    // Me
    Route::get('user', function (Request $request) {
        return $request->user();
    });
    // Route::get('user/roles', function (Request $request) {
    //     return $request->user()->getRoles();
    // });
    // Route::get('user/abilities', function (Request $request) {
    //     return $request->user()->getAbilities();
    // });

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

    // Author Followers System
    Route::get('user/following', 'AuthorFollowController@userIndex');
    Route::post('authors/{authorId}/follow/', 'AuthorFollowController@follow');
    Route::post('authors/{authorId}/unfollow/', 'AuthorFollowController@unfollow');

    // Posts
    Route::get('feed', 'PostController@feed');
    // Route::post('posts', 'PostController@store');
    Route::put('posts/{id}', 'PostController@update');
    Route::delete('posts/{id}', 'PostController@destroy');
    // Route::post('posts/uploadImage', 'PostController@uploadImage');
    Route::get('user/posts', 'PostController@userIndex');
    Route::get('user/posts/{id}', 'PostController@userShow');

    // Post Like System
    Route::post('posts/{postId}/like/', 'PostLikeController@like');
    Route::post('posts/{postId}/unlike/', 'PostLikeController@unlike');

    // Series
    Route::post('series/', 'SerieController@store');
    Route::put('series/{id}', 'SerieController@update');
    Route::post('series/{id}/updateCover', 'SerieController@updateCover');
    Route::post('series/{id}/updateBanner', 'SerieController@updateBanner');
    Route::post('series/{id}/removeBanner', 'SerieController@removeBanner');
    Route::get('user/series', 'SerieController@userIndex');
    Route::get('user/series/{id}', 'SerieController@userShow');

    // Series Like System
    Route::post('series/{serieId}/like/', 'SerieLikeController@like');
    Route::post('series/{serieId}/unlike/', 'SerieLikeController@unlike');

    // Series Subscriber System
    Route::get('user/subscriptions', 'SerieSubscribeController@userIndex');
    Route::post('series/{serieId}/subscribe/', 'SerieSubscribeController@subscribe');
    Route::post('series/{serieId}/unsubscribe/', 'SerieSubscribeController@unsubscribe');

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

    // Comments System
    Route::post('comments', 'CommentController@store');
    // Route::put('comments/{comment}', 'CommentController@update');
    Route::delete('comments/{comment}', 'CommentController@destroy');
    Route::post('comments/{comment}', 'CommentController@reply');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Strictly guest)
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'guest:api'], function () {
    // Auth
    Route::get('login', function () {
        return Response::json([ 'message' => 'Not logged-in, log-in using a POST request.' ], 401);
    })->name('login');
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

// Author Followers System
Route::get('series/{id}/followers', 'AuthorFollowController@authorIndex');

// Posts
Route::get('posts', 'PostController@index');
Route::get('randomPosts', 'PostController@random');
Route::get('posts/{id}', 'PostController@show');
Route::get('authors/{id}/posts/', 'PostController@authorIndex');

// Series
Route::get('series/', 'SerieController@index');
Route::get('newSeries/', 'SerieController@new');
Route::get('popularSeries/', 'SerieController@popular');
Route::get('series/{id}', 'SerieController@show');
Route::get('authors/{id}/series', 'SerieController@authorIndex');
Route::get('series/', 'SerieController@index');

// Series Subscriber System
Route::get('series/{id}/subscribers', 'SerieSubscribeController@serieIndex');

// Chapters
Route::get('series/{id}/chapters/', 'ChapterController@serieIndex');
Route::get('chapters/{id}', 'ChapterController@show');

// Comments
Route::get('comments', 'CommentController@index');

// Suggestions
Route::post('suggestions', 'SuggestionController@store');

// Schedule
Route::get('schedule', 'ScheduleController@index');
