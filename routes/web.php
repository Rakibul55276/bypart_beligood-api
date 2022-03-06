<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ForumController;
use App\Http\Controllers\Web\DirectoryController;
use App\Http\Controllers\Web\ListingController;
use App\Http\Controllers\Web\PointsController;
use App\Http\Controllers\Web\MakeController;
use App\Http\Controllers\FPX\Controller;

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
    return redirect('login');
});

Auth::routes();

// Our custom login routes
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.custom');

Route::prefix('/')->middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'index'])->name('profile');

    Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications');
    Route::post('/notifications', [ProfileController::class, 'notifications'])->name('notifications');

    Route::get('/allnotifications', [ProfileController::class, 'allnotifications'])->name('allnotifications');
    Route::post('/allnotifications', [ProfileController::class, 'allnotifications'])->name('allnotifications');

    Route::get('/userprofile', [ProfileController::class, 'userprofile'])->name('userprofile');
    Route::post('/userprofile', [ProfileController::class, 'userprofile'])->name('userprofile');

    Route::get('/userview', [ProfileController::class, 'userview'])->name('userview');
    Route::post('/userview', [ProfileController::class, 'userview'])->name('userview');

    Route::get('/updateUser', [ProfileController::class, 'updateUser'])->name('updateUser');
    Route::post('/updateUser', [ProfileController::class, 'updateUser'])->name('updateUser');

    Route::get('/updateCompany', [ProfileController::class, 'updateCompany'])->name('updateCompany');
    Route::post('/updateCompany', [ProfileController::class, 'updateCompany'])->name('updateCompany');

    Route::get('/changePassword', [ProfileController::class, 'changePassword'])->name('changePassword');
    Route::post('/changePassword', [ProfileController::class, 'changePassword'])->name('changePassword');

    Route::get('/users', [ProfileController::class, 'users'])->name('users');
    Route::post('/users', [ProfileController::class, 'users'])->name('users');

    Route::get('/newuser', [ProfileController::class, 'newuser'])->name('newuser');
    Route::post('/newuser', [ProfileController::class, 'newuser'])->name('newuser');

    Route::get('/addNewUser', [ProfileController::class, 'addNewUser'])->name('addNewUser');
    Route::post('/addNewUser', [ProfileController::class, 'addNewUser'])->name('addNewUser');

    Route::get('/getUsers', [ProfileController::class, 'getUsers'])->name('getUsers');
    Route::post('/getUsers', [ProfileController::class, 'getUsers'])->name('getUsers');

    Route::get('/deleteUsers', [ProfileController::class, 'deleteUsers'])->name('deleteUsers');
    Route::post('/deleteUsers', [ProfileController::class, 'deleteUsers'])->name('deleteUsers');

    Route::get('/restoreUsers', [ProfileController::class, 'restoreUsers'])->name('restoreUsers');
    Route::post('/restoreUsers', [ProfileController::class, 'restoreUsers'])->name('restoreUsers');

    Route::get('/agents', [ProfileController::class, 'agents'])->name('agents');
    Route::post('/agents', [ProfileController::class, 'agents'])->name('agents');

    Route::get('/dealer', [ProfileController::class, 'dealer'])->name('dealer');
    Route::post('/dealer', [ProfileController::class, 'dealer'])->name('dealer');

    Route::get('/admin', [ProfileController::class, 'admin'])->name('admin');
    Route::post('/admin', [ProfileController::class, 'admin'])->name('admin');

    //Makes

    Route::get('/makes', [MakeController::class, 'makeListing'])->name('makes');
    Route::post('/makes', [MakeController::class, 'makeListing'])->name('makes');

    Route::get('/makerequests', [MakeController::class, 'makeRequests'])->name('makerequests');
    Route::post('/makerequests', [MakeController::class, 'makeRequests'])->name('makerequests');

    Route::get('/deleteRequest', [MakeController::class, 'deleteRequest'])->name('deleteRequest');
    Route::post('/deleteRequest', [MakeController::class, 'deleteRequest'])->name('deleteRequest');

    Route::get('/getmakerequests', [MakeController::class, 'getMakeRequests'])->name('getmakerequests');
    Route::post('/getmakerequests', [MakeController::class, 'getMakeRequests'])->name('getmakerequests');

    Route::get('/getmakesmodels', [MakeController::class, 'getMakesModels'])->name('getmakesmodels');
    Route::post('/getmakesmodels', [MakeController::class, 'getMakesModels'])->name('getmakesmodels');

    Route::get('/editmodal', [MakeController::class, 'editModalData'])->name('editmodal');
    Route::post('/editmodal', [MakeController::class, 'editModalData'])->name('editmodal');

    Route::get('/editmakes', [MakeController::class, 'editMakes'])->name('editmakes');
    Route::post('/editmakes', [MakeController::class, 'editMakes'])->name('editmakes');

    Route::get('/addmakes', [MakeController::class, 'addMakes'])->name('addmakes');
    Route::post('/addmakes', [MakeController::class, 'addMakes'])->name('addmakes');

    //Forum

    Route::get('/forumposts', [ForumController::class, 'forumposts'])->name('forumposts');
    Route::post('/forumposts', [ForumController::class, 'forumposts'])->name('forumposts');

    Route::get('/getPosts', [ForumController::class, 'getPosts'])->name('getPosts');
    Route::post('/getPosts', [ForumController::class, 'getPosts'])->name('getPosts');

    Route::get('/deletepost', [ForumController::class, 'deletePost'])->name('deletePost');
    Route::post('/deletepost', [ForumController::class, 'deletePost'])->name('deletePost');

    Route::get('/restorepost', [ForumController::class, 'restorePost'])->name('restorePost');
    Route::post('/restorepost', [ForumController::class, 'restorePost'])->name('restorePost');

    Route::get('/editpost', [ForumController::class, 'editPost'])->name('editPost');
    Route::post('/editpost', [ForumController::class, 'editPost'])->name('editPost');

    Route::get('/modifypostdetails', [ForumController::class, 'modifyPostDetails'])->name('modifyPostDetails');
    Route::post('/modifypostdetails', [ForumController::class, 'modifyPostDetails'])->name('modifyPostDetails');

    Route::get('/forumcomments', [ForumController::class, 'forumComments'])->name('forumComments');
    Route::post('/forumcomments', [ForumController::class, 'forumComments'])->name('forumComments');

    Route::get('/getComments', [ForumController::class, 'getComments'])->name('getComments');
    Route::post('/getComments', [ForumController::class, 'getComments'])->name('getComments');

    Route::get('/deletecomment', [ForumController::class, 'deleteComment'])->name('deleteComment');
    Route::post('/deletecomment', [ForumController::class, 'deleteComment'])->name('deleteComment');

    Route::get('/restorecomment', [ForumController::class, 'restoreComment'])->name('restoreComment');
    Route::post('/restorecomment', [ForumController::class, 'restoreComment'])->name('restoreComment');

    //Common Routes

    Route::get('/dynamicimagessettings', [CommonController::class, 'dynamicImagesSettings'])->name('dynamicImagesSettings');
    Route::post('/dynamicimagessettings', [CommonController::class, 'dynamicImagesSettings'])->name('dynamicImagesSettings');

    Route::get('/addSettings', [CommonController::class, 'addSettings'])->name('addSettings');
    Route::post('/addSettings', [CommonController::class, 'addSettings'])->name('addSettings');

    Route::get('/editSettings', [CommonController::class, 'editSettings'])->name('editSettings');
    Route::post('/editSettings', [CommonController::class, 'editSettings'])->name('editSettings');

    Route::get('/updateImage', [CommonController::class, 'updateImage'])->name('updateImage');
    Route::post('/updateImage', [CommonController::class, 'updateImage'])->name('updateImage');

    //Directory

    Route::get('/categories', [DirectoryController::class, 'categories'])->name('categories');
    Route::post('/categories', [DirectoryController::class, 'categories'])->name('categories');

    Route::get('/addCategory', [DirectoryController::class, 'addCategory'])->name('addCategory');
    Route::post('/addCategory', [DirectoryController::class, 'addCategory'])->name('addCategory');

    Route::get('/editCategory', [DirectoryController::class, 'editCategory'])->name('editCategory');
    Route::post('/editCategory', [DirectoryController::class, 'editCategory'])->name('editCategory');

    Route::get('/subcategories', [DirectoryController::class, 'subcategories'])->name('subcategories');
    Route::post('/subcategories', [DirectoryController::class, 'subcategories'])->name('subcategories');

    Route::get('/addSubCategory', [DirectoryController::class, 'addSubCategory'])->name('addSubCategory');
    Route::post('/addSubCategory', [DirectoryController::class, 'addSubCategory'])->name('addSubCategory');

    Route::get('/editSubCategory', [DirectoryController::class, 'editSubCategory'])->name('editSubCategory');
    Route::post('/editSubCategory', [DirectoryController::class, 'editSubCategory'])->name('editSubCategory');

    Route::get('/updateIcon', [DirectoryController::class, 'updateIcon'])->name('updateIcon');
    Route::post('/updateIcon', [DirectoryController::class, 'updateIcon'])->name('updateIcon');

    Route::get('/uploadLogo', [DirectoryController::class, 'uploadLogo'])->name('uploadLogo');
    Route::post('/uploadLogo', [DirectoryController::class, 'uploadLogo'])->name('uploadLogo');

    Route::get('/companies', [DirectoryController::class, 'companies'])->name('companies');
    Route::post('/companies', [DirectoryController::class, 'companies'])->name('companies');

    Route::get('/getCompanies', [DirectoryController::class, 'getCompanies'])->name('getCompanies');
    Route::post('/getCompanies', [DirectoryController::class, 'getCompanies'])->name('getCompanies');

    Route::get('/editCompanyModal', [DirectoryController::class, 'editCompanyModal'])->name('editCompanyModal');
    Route::post('/editCompanyModal', [DirectoryController::class, 'editCompanyModal'])->name('editCompanyModal');

    Route::get('/editCompany', [DirectoryController::class, 'editCompany'])->name('editCompany');
    Route::post('/editCompany', [DirectoryController::class, 'editCompany'])->name('editCompany');

    Route::get('/addCompanyModal', [DirectoryController::class, 'addCompanyModal'])->name('addCompanyModal');
    Route::post('/addCompanyModal', [DirectoryController::class, 'addCompanyModal'])->name('addCompanyModal');

    Route::get('/addCompany', [DirectoryController::class, 'addCompany'])->name('addCompany');
    Route::post('/addCompany', [DirectoryController::class, 'addCompany'])->name('addCompany');


    // Listing

    Route::get('/viewlisting', [ListingController::class, 'index']);
    Route::post('/viewlisting', [ListingController::class, 'index']);

    Route::get('/updateListingStatus', [ListingController::class, 'updateListingStatus']);
    Route::post('/updateListingStatus', [ListingController::class, 'updateListingStatus']);

    Route::get('/classified', [ListingController::class, 'classified'])->name('classified');
    Route::post('/classified', [ListingController::class, 'classified'])->name('classified');

    Route::get('/auction', [ListingController::class, 'auction'])->name('auction');
    Route::post('/auction', [ListingController::class, 'auction'])->name('auction');

    Route::get('/getlisting', [ListingController::class, 'getListing'])->name('getListing');
    Route::post('/getlisting', [ListingController::class, 'getListing'])->name('getListing');

    //Points

    Route::get('/userpointsystem', [PointsController::class, 'userpointsystem'])->name('userpointsystem');
    Route::post('/userpointsystem', [PointsController::class, 'userpointsystem'])->name('userpointsystem');

    Route::get('/agentpointsystem', [PointsController::class, 'agentpointsystem'])->name('agentpointsystem');
    Route::post('/agentpointsystem', [PointsController::class, 'agentpointsystem'])->name('agentpointsystem');

    Route::get('/dealerpointsystem', [PointsController::class, 'dealerpointsystem'])->name('dealerpointsystem');
    Route::post('/dealerpointsystem', [PointsController::class, 'dealerpointsystem'])->name('dealerpointsystem');

    Route::get('/editPoint', [PointsController::class, 'editPoint'])->name('editPoint');
    Route::post('/editPoint', [PointsController::class, 'editPoint'])->name('editPoint');

});

Route::get('ByPartsFPX', [Controller::class, 'paymentDone'])->name('paymentdone');
