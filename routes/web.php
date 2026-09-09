<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryTagController;
use App\Http\Controllers\RollUserController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;

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

Route::get('/', function () {
    return view('auth.login');
});
// Country Search

Route::get('/search-country-ajax', [PostController::class, 'searchCountry'])->name('country_search');

Auth::routes();
Route::group(['middleware' => 'auth'], function () {
    //Route::get('/moveImage', [TopicController::class, 'moveimageOneTwoAnother']);
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/post-image-crop', [PostController::class, 'postImageCrop']);
    Route::get('/categories_list_ajax', [PostController::class, 'categoryListAjax']);
    Route::get('/tags_list_ajax', [PostController::class, 'tagListAjax']);
    Route::post('/summernote_image_upload', [PostController::class, 'summernoteImage'])->name('summernote_image_upload');

    Route::get('/posts', [PostController::class, 'createIndex'])->name('post_index');
    Route::get('/post/create', [PostController::class, 'createPost'])->name('post_create');
    Route::post('/post/store', [PostController::class, 'storePost'])->name('store_post');
    Route::get('/post/{id}/edit', [PostController::class, 'editPost'])->name('edit_post');
    Route::post('/post/update/{id}', [PostController::class, 'updatePost'])->name('update_post');
    Route::post('/post-status-change/{id}', [PostController::class, 'postStatusChange'])->name('post_status_change');

    // Categories
    Route::get('/categories', [CategoryTagController::class, 'categoriesIndex'])->name('category_index');
    Route::post('/categories/store', [CategoryTagController::class, 'categoryStore'])->name('category_store');
    Route::post('/categories/update', [CategoryTagController::class, 'categoryUpdate'])->name('category_update');
    Route::post('/categories/delete/{id}', [CategoryTagController::class, 'categoryDelete'])->name('category_delete');

    // Tags 
    Route::get('/tags', [CategoryTagController::class, 'tagsIndex'])->name('tag_index');
    Route::post('/tags/store', [CategoryTagController::class, 'tagStore'])->name('tag_store');
    Route::post('/tags/update', [CategoryTagController::class, 'tagUpdate'])->name('tag_update');
    Route::post('/tags/delete/{id}', [CategoryTagController::class, 'tagDelete'])->name('tag_delete');

    //Role User
    Route::get('/roles', [RollUserController::class, 'roles'])->name('roles');
    Route::post('/role/store', [RollUserController::class, 'roleStore'])->name('role_store');
    Route::post('/role/edit',[RollUserController::class, 'roleUpdate'])->name('role_edit');
    Route::post('/user-role/permission',[RollUserController::class, 'allowPermission'])->name('role_permission');

    Route::get('/users', [RollUserController::class, 'users'])->name('users');
    Route::post('/user/store', [RollUserController::class, 'userStore'])->name('user_store');
    Route::post('/user/update', [RollUserController::class, 'userUpdate'])->name('user_update');
    Route::post('/user/disable-enable/{id}',[RollUserController::class, 'userDisableEnable'])->name('user_disable_enable');
    Route::post('/user-delete/{id}',[RollUserController::class, 'userDelete'])->name('user_delete');

    //Topics for writer
    Route::get('/topics', [TopicController::class, 'topics'])->name('topics');
    Route::get('/topic/assign', [TopicController::class, 'topicAssign'])->name('topic_assign');
    Route::post('/topic/store', [TopicController::class, 'topicStore'])->name('topic_store');
    Route::get('/topic/view/{id}', [TopicController::class, 'topicView'])->name('topic_view');

    //My Post for writer

    Route::get('/my-post', [PostController::class, 'myPost'])->name('my_post');

    //Get destinations
    Route::get('/dook-destinations-pull', [DestinationController::class, 'getDestintionFromDook'])->name('dook_destinations_pull');
    Route::get('/destinations', [DestinationController::class, 'destintions'])->name('dook_destinations');
    Route::post('/destination_assign/{id}/{writer_id}', [DestinationController::class, 'destintionAssign'])->name('destination_assign');

    Route::get('/destination/{id}/edit', [DestinationController::class, 'destinationEdit'])->name('edit_destination');
    Route::get('/destination/{id}/view', [DestinationController::class, 'destinationView'])->name('view_destination');
    Route::post('/destination/{id}/update', [DestinationController::class, 'destinationUpdate'])->name('update_destination');
    Route::post('/destination_status_change', [DestinationController::class, 'destinationStatusChanged'])->name('destination_status_change');

    Route::post('/dest-suggetion', [DestinationController::class, 'destinationSuggetionStore'])->name('destination_suggetion_store');
    Route::get('/destination-suggetions', [DestinationController::class, 'destinationSuggetions'])->name('destination_suggetion');
    Route::get('/topic-suggetions', [TopicController::class, 'topicSuggetions'])->name('topic_suggetion');

    //FCM token notifications
    Route::post('/fcm-token-update', [NotificationController::class, 'updateFCMToken'])->name('fcmUpdate');
    Route::post('/store-notification', [NotificationController::class, 'storeNotification'])->name('store_notification');
    Route::post('/notification-status-change/{id}', [NotificationController::class, 'changeStatusNotification'])->name('notification_status_change');
    Route::get('/notifications', [NotificationController::class, 'getNotification'])->name('get_notification');

    //Profile

    Route::get('/company-profile', [ProfileController::class, 'companyProfile'])->name('company_profile');
    Route::post('/company-profile/store', [ProfileController::class, 'companyProfileStore'])->name('company_profile_store');

    Route::get('get_post',[TopicController::class,'getBlogWP']);
});