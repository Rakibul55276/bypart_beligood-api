<?php

use App\Http\Controllers\CommonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\FinanceInsurenceController;
use App\Http\Controllers\Api\MakeController;
use App\Http\Controllers\Api\Directory\DirectoryController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Listing\CarListingController;
use App\Http\Controllers\Api\Listing\CarListingQAController;
use App\Http\Controllers\Api\Listing\CarAuctionController;
use App\Http\Controllers\Api\Payment\PointDeductionController;
use App\Http\Controllers\Api\Payment\FpxController;
use App\Http\Controllers\FPX\Controller;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('email/verify', [AuthController::class, 'verifyUser']);
Route::post('email/resend', [AuthController::class, 'sendAccountVerificationEmail']);
Route::post('forgot-password', [AuthController::class, 'sentForgotPasswordEmailLink']);
Route::post('update-password', [AuthController::class, "updatePassword"]);
// This controller used in middleware/Authetication.php to redirect
Route::get('not-authenticated', [AuthController::class, 'notAuthenticated']);

Route::post('login/{provider}/callback', [AuthController::class, 'handleProviderCallBack']);

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [ProfileController::class, 'logout']);
    Route::get('user', [ProfileController::class, "getUserData"]);
    Route::post('password', [ProfileController::class, "changePassword"]);
    Route::post('updateuser', [ProfileController::class, "updatePersonalUserData"]);
    // send actual status to get data
    Route::get('mylisting/{status}/{limit?}', [ProfileController::class, "getUserAdsList"]);
    // send 1=is_won, 2=is_lost, 3=is_favorite, 4=ongoing_auction_participation
    Route::get('buying/{status}/{limit?}', [ProfileController::class, "getUserBuyingList"]);

    Route::get('getcompany', [ProfileController::class, "getUserCompanyDetails"]);
    Route::post('updatecompany', [ProfileController::class, "updateUserCompanyDetails"]);

    Route::post('mobile/verify', [AuthController::class, 'verifyUserMobile']);
    Route::post('mobile/sendtac', [AuthController::class, 'sendTacToUserMobile']);

    // For mark as sold or delete
    Route::post('statusupdateuserlisting', [ProfileController::class, 'statusUpdateUserListing']);

    //For promoting add
    Route::post('promoteads', [ProfileController::class, 'promoteAds']);

    //To get if user can perform certain action or not base on their verification status
    // Send `is_verified_for` and value either bidding or listing
    Route::post('isuserverified', [ProfileController::class, 'isUserFullyVerified']);
});

//Notification Routes
Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['auth:sanctum']], function () {
    Route::get('getnotifications', [NotificationController::class, "getNotifications"]);
    Route::post('markasread', [NotificationController::class, "markNotificationAsRead"]);
    Route::get('markallasread', [NotificationController::class, "markAllAsRead"]);
});

//Forum Routes
Route::group(['prefix' => 'forum', 'as' => 'forum.', 'middleware' => ['auth:sanctum']], function () {

    Route::get('getposts', [ForumController::class, "getPosts"]);
    Route::post('getposts', [ForumController::class, "getPosts"]);

    Route::get('getindividualpost', [ForumController::class, "getIndividualPost"]);
    Route::post('getindividualpost', [ForumController::class, "getIndividualPost"]);

    Route::get('toptrendingtopic', [ForumController::class, "topTrendingTopic"]);
    Route::post('toptrendingtopic', [ForumController::class, "topTrendingTopic"]);

    Route::get('getcomments', [ForumController::class, "getComments"]);
    Route::post('getcomments', [ForumController::class, "getComments"]);

    Route::get('liketransaction', [ForumController::class, "likeTransaction"]);
    Route::post('liketransaction', [ForumController::class, "likeTransaction"]);

    Route::get('getcategorylist', [ForumController::class, "getCategoryList"]);
    Route::post('getcategorylist', [ForumController::class, "getCategoryList"]);

    Route::get('addpost', [ForumController::class, "addPost"]);
    Route::post('addpost', [ForumController::class, "addPost"]);

    Route::get('addcomment', [ForumController::class, "addComment"]);
    Route::post('addcomment', [ForumController::class, "addComment"]);

    Route::get('addreply', [ForumController::class, "addReply"]);
    Route::post('addreply', [ForumController::class, "addReply"]);

    Route::get('getreplies', [ForumController::class, "getReplies"]);
    Route::post('getreplies', [ForumController::class, "getReplies"]);

    Route::get('deletepost', [ForumController::class, "deletePost"]);
    Route::post('deletepost', [ForumController::class, "deletePost"]);

    Route::get('deletecomment', [ForumController::class, "deleteComment"]);
    Route::post('deletecomment', [ForumController::class, "deleteComment"]);

    Route::get('reportpost', [ForumController::class, "reportPost"]);
    Route::post('reportpost', [ForumController::class, "reportPost"]);
});

//Directory Route

Route::group(['prefix' => 'directory', 'as' => 'directory.', 'middleware' => ['auth:sanctum']], function () {

    Route::get('getpopularcategories', [DirectoryController::class, "getPopularCategories"]);
    Route::post('getpopularcategories', [DirectoryController::class, "getPopularCategories"]);

    Route::get('getallcategories', [DirectoryController::class, "getAllCategories"]);
    Route::post('getallcategories', [DirectoryController::class, "getAllCategories"]);

    Route::get('getcompanies', [DirectoryController::class, "getCompanies"]);
    Route::post('getcompanies', [DirectoryController::class, "getCompanies"]);

    Route::get('getrecommended', [DirectoryController::class, "getRecomendedMerchants"]);
    Route::post('getrecommended', [DirectoryController::class, "getRecomendedMerchants"]);

    Route::get('directoryscript', [DirectoryController::class, "directoryScript"]);
    Route::post('directoryscript', [DirectoryController::class, "directoryScript"]);

    Route::get('basicDataPolulation', [DirectoryController::class, "basicDataPolulation"]);
    Route::post('basicDataPolulation', [DirectoryController::class, "basicDataPolulation"]);
});

//Common Controller

Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::get('getsettings', [CommonController::class, "getDynamicSettings"]);
    Route::post('getsettings', [CommonController::class, "getDynamicSettings"]);
});


Route::group(['prefix' => 'listing', 'as' => 'listing.', 'middleware' => ['auth:sanctum']], function () {
    Route::post('save-listing', [CarListingController::class, 'saveListing']);
    Route::post('update-listing', [CarListingController::class, 'updateListing']);
    Route::get('get-single-list/{list_id}/{edit}', [CarListingController::class, 'getSingleListItem']);
    Route::post('relist-without-edit', [CarListingController::class, 'relistWithoutEdit']);

    Route::post('save-draft', [CarListingController::class, 'saveDraftListing']);

    Route::post('search', [CarListingController::class, 'searchListing']);

    //Auction realted
    Route::post('auction/placebid', [CarAuctionController::class, 'placeBid']);
    Route::post('auction/buynow', [CarAuctionController::class, 'buyNow']);
    Route::get('auction/allbid/{listing_id}', [CarAuctionController::class, 'getAllBidListingWithUserById']);
    Route::post('auction/cancelautobid', [CarAuctionController::class, 'cancelAutoBid']);
    //Question related
    Route::post('qa/save-question', [CarListingQAController::class, 'saveQuestion']);
    Route::post('qa/save-reply', [CarListingQAController::class, 'saveReply']);
    Route::post('qa/delete-question', [CarListingQAController::class, 'deleteQuestion']);
    Route::post('qa/delete-question-reply', [CarListingQAController::class, 'deleteQuestionReply']);

    // Listing user activity
    Route::post('markunmark-as-favorite', [CarListingController::class, 'markOrUnmarkAsFavourite']);

    // Route incase user did not find make, so they can submit through this route
    Route::post('save-make-request', [MakeController::class, 'saveMakeRequestForAdminReview']);

    //Reveal contact from single list, seller_info -> when buyer want to see, buyer_info -> when seller want to see
    Route::post('show-contact/{seller_info_or_buyer_info}', [CarListingController::class, 'ifShowContactDeductPoint']);
});
// listing realted if not auth
Route::group(['prefix' => 'listing', 'as' => 'listing.'], function () {
    //Get Categories
    Route::get('categories', [CarListingController::class, 'getCategories']);

    //Make related
    Route::get('make', [MakeController::class, 'getAllMake']);
    Route::post('model', [MakeController::class, 'getAllModel']);
    Route::post('manufactured-year', [MakeController::class, 'getManufacturedYear']);
    Route::post('fuel-type', [MakeController::class, 'getFuelType']);
    Route::post('variant', [MakeController::class, 'getVariant']);
    Route::post('car-body-type', [MakeController::class, 'getCarBodyType']);
    Route::post('engine-size', [MakeController::class, 'getEngineSize']);
    //Route::post('seats', [MakeController::class, 'getCarSeats']);
    Route::post('doors', [MakeController::class, 'getCarDoors']);

    //Make related get from listing
    Route::get('filter/make', [MakeController::class, 'getAllMakeFromListing']);
    Route::post('filter/car-body-type', [MakeController::class, 'getCarBodyTypeFromListing']);
    Route::post('filter/model', [MakeController::class, 'getModelFromListing']);
    Route::post('filter/fuel-type', [MakeController::class, 'getFuelTypeFromListing']);
    Route::post('filter/engine-size', [MakeController::class, 'getEngineSizeFromListing']);
    Route::post('filter/manufacture-year', [MakeController::class, 'getYearFromListing']);

    //Listing view count ( since this can view by any user )
    Route::post('viewcount', [CarListingController::class, 'listViewCount']);
    //Other listing realted pages api route
    Route::get('other-listing-user-may-like', [CarListingController::class, 'otherListingMayUserLike']);
    Route::get('popular-listing/{categories_id}', [CarListingController::class, 'popularListing']);
    Route::get('recently-added', [CarListingController::class, 'recentlyAdded']);
    Route::get('auction-closing-soon/{categories_id}', [CarListingController::class, 'auctionClosingSoon']);
    // Since user can filter without login, so we put outside of auth sacntum
    Route::post('filter', [CarListingController::class, 'filterListing']);
    Route::get('get-single-list/{list_id}', [CarListingController::class, 'getSingleListItem']);
    //Classified listing realted
    Route::post('classified/sendemailtoseller', [CarListingController::class, 'sendEmailforLisitingClassified']);

    //Question related non authenticated route
    Route::get('qa/getquestionforsinglelist/{list_id}', [CarListingQAController::class, 'getQuestionForSingleListing']);
    Route::get('total-published-ads', [CarListingController::class, 'getTotalPublishedAds']);
});

Route::group(['prefix' => 'point', 'as' => 'point.', 'middleware' => ['auth:sanctum']], function () {
    Route::get('test', [PointDeductionController::class, 'test']);
    // Send action to get if user can perform this
    Route::post('isallow', [PointDeductionController::class, 'isAllowToPerformThisAction']);
    Route::get('getuserpoint', [PointDeductionController::class, 'getUserPoint']);
    Route::get('point-transaction-type', [PointDeductionController::class, 'getPointTransactionType']);
    Route::get('userpoint-with-type', [PointDeductionController::class, 'getUserPointWithType']);
    Route::get('userpoint-history/{limit?}', [PointDeductionController::class, 'getUserPointHistory']);
    Route::get('user-lock-point/{limit?}', [PointDeductionController::class, 'getUserLockPoint']);
    //Payment related
    Route::get('bank-list', [FpxController::class, 'getBankList']);
    Route::post('initpayment', [Controller::class, 'initiatePayment']);

    Route::get('history', [FpxController::class, 'getTransactionHistory']);
    Route::post('history', [FpxController::class, 'getTransactionHistory']);
});

//Feed related routes
Route::get('getarticlenews', [FeedController::class, 'getArticleNewsFeed']);
Route::get('getnewcarnews', [FeedController::class, 'getNewCarFeed']);
//Finance Insurance related routes
Route::post('submitcarloanform', [FinanceInsurenceController::class, 'submitCarLoanForm']);
Route::post('submitextendwarrantyform', [FinanceInsurenceController::class, 'submitExtendWarrantyForm']);
Route::post('submitinsuranceform', [FinanceInsurenceController::class, 'submitInsuranceForm']);
