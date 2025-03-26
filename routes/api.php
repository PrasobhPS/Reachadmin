<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\SpecialistsController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\StripeWebhookController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes

Route::middleware('api.key')->group(function () {
    // Your protected routes here
    Route::post('login', [LoginController::class, 'login']);
    Route::post('ios-login', [LoginController::class, 'iosLogin']);
    Route::post('register', [RegisterController::class, 'signup']);
    Route::post('contactUs', [CommonController::class, 'contactUs']);
    //Route::get('getDialCode', [CommonController::class, 'getDialCode']);
    Route::get('getCountries', [CommonController::class, 'getCountries']);
    //Route::get('job-role', [JobController::class, 'getJobRole']);
    //Route::get('job-duration', [JobController::class, 'getJobDuration']);
    //Route::get('boat-location', [JobController::class, 'getBoatLocation']);
    //Route::get('boat-type', [JobController::class, 'getBoatType']);
    //Route::get('getLanguages', [CommonController::class, 'getLanguages']);
    //Route::get('getQualifications', [CommonController::class, 'getQualifications']);
    //Route::get('getExperience', [CommonController::class, 'getExperience']);
    //Route::get('getAvailability', [CommonController::class, 'getAvailability']);
    //Route::get('getPositions', [CommonController::class, 'getPositions']);
    //Route::get('getSalaryExpectations', [CommonController::class, 'getSalaryExpectations']);
    Route::get('getCmsContents', [CommonController::class, 'getCmsContents']);
    Route::get('ourPartners', [CommonController::class, 'ourPartners']);
    Route::get('membershipFee', [CommonController::class, 'membershipFee']);
    Route::post('chatReportMember', [CommonController::class, 'chatReportMember']);
    Route::post('validateReferralCode', [CommonController::class, 'validateReferralCode']);
    Route::get('getAppHomeDetails', [CommonController::class, 'getAppHomeDetails']);
    Route::get('getSiteHomeDetails', [CommonController::class, 'getSiteHomeDetails']);

    Route::post('forgotPassword', [RegisterController::class, 'forgotPassword']);
    Route::post('resetPassword', [RegisterController::class, 'resetPassword']);
    Route::post('checkMemberEmailExists', [RegisterController::class, 'checkMemberEmailExists']);
    Route::post('subscribeNewsletter', [RegisterController::class, 'subscribeNewsletter']);
    Route::post('checkTokenExists', [RegisterController::class, 'checkTokenExists']);
    // Route::post('makeSecondPayment', [RegisterController::class, 'makeSecondPayment']);

    Route::post('update-membership', [RegisterController::class, 'updateMembership']);

    // Specialists Routes
    Route::get('getSpecialistsProfile/{id}', [SpecialistsController::class, 'getSpecialistsProfile']);
    Route::get('getSpecialistsVideos/{id}', [SpecialistsController::class, 'getSpecialistsVideos']);
    Route::get('join-reach', [CommonController::class, 'join_reach']);
    Route::post('referral-discount', [CommonController::class, 'referral_discount']);
    Route::post('currencyConvert', [CommonController::class, 'currencyConvert']);
    Route::post('getExchangeRates', [CommonController::class, 'getExchangeRates']);
    Route::get('specialists/getAllRatings/{id}', [SpecialistsController::class, 'getAllRatings']);

    Route::post('emailVerify', [RegisterController::class, 'emailVerify']);
    Route::post('resendEmail', [RegisterController::class, 'resendEmail']);
    Route::post('membership-features', [CommonController::class, 'membershipFeaturs']);
});

Route::middleware('api.key.or.auth')->group(function () {
    Route::post('paid-registration', [RegisterController::class, 'paidRegistration']);
    Route::get('getSpecialistsList', [SpecialistsController::class, 'getSpecialistsList']);
    Route::post('update-registration', [RegisterController::class, 'updateRegistration']);
});


// Protected Routes (Require Authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Logout Route
    Route::post('logout', [LoginController::class, 'logout']);
    Route::get('clubHouse', [CommonController::class, 'clubHouse']);
    Route::get('chandlery', [CommonController::class, 'chandlery']);
    Route::post('getCouponCode', [CommonController::class, 'getCouponCode']);

    Route::get('getPaymentInfo', [CommonController::class, 'getPaymentInfo']);
    // Member Routes
    Route::prefix('member')->group(function () {
        Route::get('getProfile', [MemberController::class, 'getProfile']);
        Route::post('addProfilePicture', [MemberController::class, 'addProfilePicture']);
        Route::post('updateProfile', [MemberController::class, 'updateProfile']);
        Route::post('removePicture', [MemberController::class, 'removePicture']);
        Route::post('deactivateProfile', [MemberController::class, 'deactivateProfile']);
        Route::post('deleteProfile', [MemberController::class, 'deleteProfile']);

        Route::post('changePassword', [MemberController::class, 'changePassword']);
        Route::post('chunkUpload', [MemberController::class, 'chunkUpload']);
        Route::post('updateStatus', [MemberController::class, 'updateStatus']);
        Route::get('bookingHistory', [MemberController::class, 'bookingHistory']);
        Route::get('getMemberDetails/{id}', [MemberController::class, 'getMemberDetails']);
        Route::post('getChatMemberDetails', [MemberController::class, 'getChatMemberDetails']);
        Route::get('getDashboardCount', [MemberController::class, 'getDashboardCount']);
        //Route::get('myBlockedList', [MemberController::class, 'myBlockedList']);
        Route::post('CallScheduleWithSpecialist', [MemberController::class, 'CallScheduleWithSpecialist']);
        Route::get('myReferralList', [MemberController::class, 'myReferralList']);
        //Route::post('BlockScheduleWithSpecialist', [MemberController::class, 'BlockScheduleWithSpecialist']);
        //Route::get('getUnavialableList', [MemberController::class, 'getUnavialableList']);
        Route::post('setWorkingHours', [MemberController::class, 'setWorkingHours']);
        Route::post('unavailableList', [MemberController::class, 'unavailableList']);
        Route::get('getWorkingHours', [MemberController::class, 'getWorkingHours']);
        Route::get('getUnavailableList', [MemberController::class, 'getUnavailableList']);
        Route::post('updateWorkingHours', [MemberController::class, 'updateWorkingHours']);
        Route::post('deleteWorkingHours', [MemberController::class, 'deleteWorkingHours']);
        //Route::post('updateUnavilableList', [MemberController::class, 'updateUnavilableList']);
        Route::post('deleteUnavilableList', [MemberController::class, 'deleteUnavilableList']);
        Route::get('unsubscribePlan', [MemberController::class, 'unsubscribePlan']);
        Route::post('notificationList', [MemberController::class, 'notificationList']);
        Route::post('readNotification', [MemberController::class, 'readNotification']);
        Route::get('createStripeAccount', [MemberController::class, 'createStripeAccount']);
        Route::post('withdrawAmount', [MemberController::class, 'withdrawAmount']);
        Route::get('createStripeVerification', [MemberController::class, 'createStripeVerification']);
        Route::post('extendslotAvailable', [MemberController::class, 'extendslotAvailable']);
        Route::get('transactionhistory', [MemberController::class, 'transactionhistory']);
        Route::get('/meeting_ended/{id}', [StripeWebhookController::class, 'expertPaymentTransfer']);
        Route::post('meetingJointime', [MemberController::class, 'meeting_jointime']);
        Route::post('meetinglefttime', [MemberController::class, 'meeting_lefttime']);
        Route::get('getFullmemberList', [MemberController::class, 'getFullmemberList']);
        Route::get('getDocVerificationStatus', [MemberController::class, 'getDocVerificationStatus']);
        Route::get('getspecialistCallpayment', [MemberController::class, 'getspecialistCallpayment']);
        Route::get('update_specialist_amount', [MemberController::class, 'update_specialist_amount']);

        Route::post('varifyIosPaymentToken', [MemberController::class, 'varifyIosPaymentToken']);
        Route::get('getmemberCardDetails', [MemberController::class, 'getmemberCardDetails']);
        Route::post('generateIostoken', [MemberController::class, 'generateIostoken']);
        Route::post('payment_card_change', [MemberController::class, 'payment_card_change']);
        Route::get('generatePdf', [MemberController::class, 'generatePdf']);
        Route::get('country_iso', [MemberController::class, 'getCountryIso']);
        Route::get('alert_notification', [MemberController::class, 'alert_notification']);
        Route::post('videoDuration', [MemberController::class, 'videoDuration']);
    });

    // Employer Routes
    Route::prefix('employer')->group(function () {
        //Route::post('registration', [EmployerController::class, 'registration']);
        Route::post('post-job', [JobController::class, 'postJob']);
        Route::get('getPostJobDetails', [JobController::class, 'getPostJobDetails']);
        Route::get('getLiveCampaigns', [JobController::class, 'getLiveCampaigns']);
        Route::post('campaignMatchesList', [JobController::class, 'campaignMatchesList']);
        Route::get('editPostJobDetails/{id}', [JobController::class, 'editPostJobDetails']);
        //Route::get('editSearchParameters/{id}', [JobController::class, 'editSearchParameters']);
        Route::get('pauseCampaign/{id}/{status}', [JobController::class, 'pauseCampaign']);
        //Route::get('cloneCampaign/{id}', [JobController::class, 'cloneCampaign']);
        Route::get('deleteCampaign/{id}', [JobController::class, 'deleteCampaign']);
        Route::get('removeCampaign/{id}', [JobController::class, 'removeCampaign']);
        Route::get('activateCampaign/{id}', [JobController::class, 'activateCampaign']);
        //Route::get('reviewJobDetails/{id}', [JobController::class, 'reviewJobDetails']);
        Route::get('previewJobAdvert/{id}', [JobController::class, 'previewJobAdvert']);
        Route::get('myMatchesList/{id}', [JobController::class, 'myMatchesList']);
        Route::get('getDraftCampaigns', [JobController::class, 'getDraftCampaigns']);
        Route::get('getArchiveCampaigns', [JobController::class, 'getArchiveCampaigns']);
        Route::post('likeCampaign', [JobController::class, 'likeCampaign']);
        Route::post('unlikeCampaign', [JobController::class, 'unlikeCampaign']);
        Route::post('removeImage', [JobController::class, 'removeImage']);
        Route::get('myLikedList/{id}', [JobController::class, 'myLikedList']);
        Route::get('myDislikedList/{id}', [JobController::class, 'myDislikedList']);

        Route::post('bookAInterview', [EmployerController::class, 'bookAInterview']);
        Route::post('bookedInterviews', [EmployerController::class, 'bookedInterviews']);
        Route::post('acceptInterview', [EmployerController::class, 'acceptInterview']);
    });

    // Employee Routes
    Route::prefix('employee')->group(function () {
        Route::get('setUpProfile', [EmployeeController::class, 'setUpProfile']);
        Route::post('saveProfile', [EmployeeController::class, 'saveProfile']);
        Route::get('reviewProfile', [EmployeeController::class, 'reviewProfile']);
        Route::get('dashboard', [EmployeeController::class, 'dashboard']);
        Route::get('myMatchesList', [EmployeeController::class, 'myMatchesList']);
        Route::get('viewAvailableJobs', [EmployeeController::class, 'viewAvailableJobs']);
        Route::post('availableJobsList', [EmployeeController::class, 'availableJobsList']);
        //Route::get('previewProfile/{id}', [EmployeeController::class, 'previewProfile']);
        Route::get('editsetUpProfile/{id}', [EmployeeController::class, 'editsetUpProfile']);
        Route::post('likeEmployee', [EmployeeController::class, 'likeEmployee']);
        Route::post('unlikeEmployee', [EmployeeController::class, 'unlikeEmployee']);
        Route::post('employeeSetStatus', [EmployeeController::class, 'employeeSetStatus']);
        Route::get('myLikedList', [EmployeeController::class, 'myLikedList']);
        Route::get('myDislikedList', [EmployeeController::class, 'myDislikedList']);
        Route::get('myInterviewList', [EmployeeController::class, 'myInterviewList']);
        Route::get('jobInterviewList', [EmployeeController::class, 'jobInterviewList']);
        //Route::get('jobInterviewCount', [EmployeeController::class, 'jobInterviewCount']);
        //Route::get('updateInterviewStatus', [EmployeeController::class, 'updateInterviewStatus']);
        Route::post('getAvailableInterviewSlots', [EmployeeController::class, 'getAvailableInterviewSlots']);
    });

    // Specialists Routes
    Route::prefix('specialists')->group(function () {
        Route::post('bookACall', [SpecialistsController::class, 'bookACall']);
        Route::post('bookACallIos', [SpecialistsController::class, 'bookACallIos']);
        //Route::post('CallScheduleList', [SpecialistsController::class, 'CallScheduleList']);
        Route::get('bookingHistory', [SpecialistsController::class, 'bookingHistory']);
        Route::post('acceptBooking', [SpecialistsController::class, 'acceptBooking']);
        Route::post('cancelBooking', [SpecialistsController::class, 'cancelBooking']);
        //Route::post('payment-card-details', [SpecialistsController::class, 'paymentCardDetails']);
        //Route::post('getMeetingLink', [SpecialistsController::class, 'getMeetingLink']);
        Route::post('setSpecialistCallRate', [SpecialistsController::class, 'setSpecialistCallRate']);
        Route::get('getSpecialistCallRate', [SpecialistsController::class, 'getSpecialistCallRate']);
        Route::post('getAvailableTimeSlots', [SpecialistsController::class, 'getAvailableTimeSlots']);
        Route::post('test_notification', [SpecialistsController::class, 'test_notification']);
        Route::post('specialistRating', [SpecialistsController::class, 'specialistRating']);
        Route::post('specialistAlreadyRated', [SpecialistsController::class, 'specialistAlreadyRated']);
        Route::post('reserveACall', [SpecialistsController::class, 'reserveACall']);
        Route::post('updateBookingStatus', [SpecialistsController::class, 'updateBookingStatus']);
    });
});

// Route to get authenticated user details
Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});
