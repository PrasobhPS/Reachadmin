<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->get('/', function () {
    return view('auth/login');
});


Route::post('/stripe/webhook', [App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);
Route::post('/stripe/documentVerification', [App\Http\Controllers\StripeWebhookController::class, 'documentVerification']);
Route::get('/reach-meeting/{id}/{meeting_id}', [App\Http\Controllers\MeetingController::class, 'index']);

Auth::routes();

Route::get('generatePdf', [App\Http\Controllers\Api\MemberController::class, 'generatePdf']);
/*
* Web routes for admin section
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('profile');
    Route::get('/changePassword', [App\Http\Controllers\AdminController::class, 'changePassword'])->name('changePassword');
    Route::post('/update-password', [App\Http\Controllers\AdminController::class, 'updatePassword'])->name('update_password');

    /*
    * Web routes for Member section
    */
    Route::get('/home', [App\Http\Controllers\MemberController::class, 'index'])->name('home');
    Route::get('/referral_rate/{id}', [App\Http\Controllers\MemberController::class, 'referral_rate'])->name('get-referal-rate');
    Route::post('member/update_referral_type', [App\Http\Controllers\MemberController::class, 'update_referral_type'])->name('update-referal-type');
    Route::get('/add-member', [App\Http\Controllers\MemberController::class, 'add_member'])->name('add-member');
    Route::post('/save-member', [App\Http\Controllers\MemberController::class, 'save_member'])->name('save-member');
    Route::get('/member/edit/{id}', [App\Http\Controllers\MemberController::class, 'edit_member'])->name('member-edit');
    Route::post('/update-member/{id}', [App\Http\Controllers\MemberController::class, 'update_member'])->name('update-member');
    Route::get('/member/delete/{id}', [App\Http\Controllers\MemberController::class, 'member_delete'])->name('member-delete');
    Route::get('/member/view/{id}', [App\Http\Controllers\MemberController::class, 'view_member'])->name('member-view');
    Route::get('/member/member-history/{id}', [App\Http\Controllers\MemberController::class, 'member_history'])->name('member-history');
    Route::get('/member/member-transaction/{id}', [App\Http\Controllers\MemberController::class, 'member_transaction'])->name('member-transaction');
    Route::get('/member/deleted', [App\Http\Controllers\MemberController::class, 'deletedMembers'])->name('member-deleted');
    Route::post('/member/active/{id}', [App\Http\Controllers\MemberController::class, 'member_active'])->name('member-active');

    Route::get('/employer', [App\Http\Controllers\MemberController::class, 'employer_list'])->name('employer');
    Route::get('/employer/view/{id}', [App\Http\Controllers\MemberController::class, 'view_employer'])->name('employer-view');
    Route::get('/employer/delete/{id}', [App\Http\Controllers\MemberController::class, 'employer_delete'])->name('employer-delete');

    Route::get('/employee', [App\Http\Controllers\MemberController::class, 'employee_list'])->name('employee');
    Route::get('/employee/view/{id}', [App\Http\Controllers\MemberController::class, 'view_employee'])->name('employee-view');
    Route::get('/employee/delete/{id}', [App\Http\Controllers\MemberController::class, 'employee_delete'])->name('employee-delete');
    Route::get('/member/resendEmail/{id}', [App\Http\Controllers\MemberController::class, 'resendEmail'])->name('resendEmail');

    /*
    * Web routes for Event section
    */
    Route::get('/events', [App\Http\Controllers\EventsController::class, 'index'])->name('events');
    Route::get('/add-event', [App\Http\Controllers\EventsController::class, 'add_event'])->name('add-event');
    Route::post('/save-event', [App\Http\Controllers\EventsController::class, 'save_event'])->name('save-event');
    Route::get('/event/edit/{id}', [App\Http\Controllers\EventsController::class, 'edit_event'])->name('event-edit');
    Route::post('/update-event/{id}', [App\Http\Controllers\EventsController::class, 'update_event'])->name('update-event');
    Route::get('/event/delete/{id}', [App\Http\Controllers\EventsController::class, 'delete_event'])->name('event-delete');

    /*
    * Web routes for Specialist section
    */
    Route::get('/specialists', [App\Http\Controllers\SpecialistController::class, 'index'])->name('specialists');
    Route::get('/add-specialist', [App\Http\Controllers\SpecialistController::class, 'add_specialist'])->name('add-specialist');
    Route::post('/save-specialist', [App\Http\Controllers\SpecialistController::class, 'save_specialist'])->name('save-specialist');
    Route::get('/specialist/edit/{id}', [App\Http\Controllers\SpecialistController::class, 'edit_specialist'])->name('specialist-edit');
    Route::post('/update-specialist/{id}', [App\Http\Controllers\SpecialistController::class, 'update_specialist'])->name('update-specialist');
    Route::get('/specialist/delete/{id}', [App\Http\Controllers\SpecialistController::class, 'delete_specialist'])->name('specialist-delete');
    Route::post('/specialist/chunk', [App\Http\Controllers\SpecialistController::class, 'uploadChunk'])->name('specialist-chunk');

    /*
    * Web routes for Specialist videos
    */
    Route::get('/specialists-videos/{id}', [App\Http\Controllers\SpecialistController::class, 'specialists_videos'])->name('specialists-videos');
    Route::get('/add-specialist-videos/{id}', [App\Http\Controllers\SpecialistController::class, 'add_specialist_videos'])->name('add-specialist-videos');
    Route::post('/save-video', [App\Http\Controllers\SpecialistController::class, 'save_video'])->name('save-video');
    Route::get('/specialists-videos/edit/{id}', [App\Http\Controllers\SpecialistController::class, 'edit_specialist_video'])->name('edit-specialist-video');
    Route::post('/update-specialist-video/{id}', [App\Http\Controllers\SpecialistController::class, 'update_video'])->name('update-specialist-video');
    Route::get('/specialists-videos/delete/{id}', [App\Http\Controllers\SpecialistController::class, 'delete_video'])->name('delete-specialist-video');
    Route::get('/specialist/scheduled-call', [App\Http\Controllers\SpecialistController::class, 'scheduled_call'])->name('scheduled-call');
    Route::post('/specialist/cancel-call/{id}', [App\Http\Controllers\SpecialistController::class, 'cancel_call'])->name('cancel-call');
    Route::get('/specialist/member-details/{id}', [App\Http\Controllers\SpecialistController::class, 'memberDetails'])->name('member-details');

    Route::get('/specialists-history/{id}', [App\Http\Controllers\SpecialistController::class, 'specialists_history'])->name('specialists-history');
    Route::post('/specialist/update-scheduled-call/{id}', [App\Http\Controllers\SpecialistController::class, 'updateScheduledCall'])->name('update-scheduled-call');

    /*
    * Web routes for Specialist Schedule
    */
    Route::get('/schedule/add/{id}', [App\Http\Controllers\ScheduleController::class, 'index'])->name('schedule-add');
    Route::post('/schedule/time-slots', [App\Http\Controllers\ScheduleController::class, 'timeSlots'])->name('time-slots');
    Route::post('/schedule/save', [App\Http\Controllers\ScheduleController::class, 'saveSchedule'])->name('schedule-save');


    /*
    * Web routes for Jobs
    */
    Route::get('/jobs', [App\Http\Controllers\JobController::class, 'index'])->name('jobs');
    Route::get('/add-job', [App\Http\Controllers\JobController::class, 'add_job'])->name('add-job');
    Route::post('/save-job', [App\Http\Controllers\JobController::class, 'save_job'])->name('save-job');
    Route::get('/job/edit/{id}', [App\Http\Controllers\JobController::class, 'edit_job'])->name('job-edit');
    Route::post('/update-job/{id}', [App\Http\Controllers\JobController::class, 'update_job'])->name('update-job');
    Route::get('/job/delete/{id}', [App\Http\Controllers\JobController::class, 'delete_job'])->name('delete-job');

    /*
    * Web routes for Partners
    */
    Route::get('/partners', [App\Http\Controllers\PartnerController::class, 'index'])->name('partners');
    Route::get('/add-partner', [App\Http\Controllers\PartnerController::class, 'add_partner'])->name('add-partner');
    Route::post('/save-partner', [App\Http\Controllers\PartnerController::class, 'save_partner'])->name('save-partner');
    Route::get('/partner/edit/{id}', [App\Http\Controllers\PartnerController::class, 'edit_partner'])->name('partner-edit');
    Route::post('/update-partner/{id}', [App\Http\Controllers\PartnerController::class, 'update_partner'])->name('update-partner');
    Route::get('/partner/delete/{id}', [App\Http\Controllers\PartnerController::class, 'delete_partner'])->name('delete-partner');
    Route::get('/partners/chandlery', [App\Http\Controllers\PartnerController::class, 'chandlery'])->name('partners-chandlery');
    Route::get('/partner/delete-video/{id}', [App\Http\Controllers\PartnerController::class, 'deleteVideo'])->name('delete-video');
    Route::get('/partner/delete-images/{id}/{name?}', [App\Http\Controllers\PartnerController::class, 'deleteImages'])->name('delete-images');

    /*
    * Web routes for Chandlery
    */
    Route::get('/chandlery', [App\Http\Controllers\ChandleryController::class, 'index'])->name('chandlery');
    Route::get('/add-chandlery', [App\Http\Controllers\ChandleryController::class, 'add_chandlery'])->name('add-chandlery');
    Route::post('/save-chandlery', [App\Http\Controllers\ChandleryController::class, 'save_chandlery'])->name('save-chandlery');
    Route::get('/chandlery/edit/{id}', [App\Http\Controllers\ChandleryController::class, 'edit_chandlery'])->name('chandlery-edit');
    Route::post('/update-chandlery/{id}', [App\Http\Controllers\ChandleryController::class, 'update_chandlery'])->name('update-chandlery');
    Route::get('/chandlery/delete/{id}', [App\Http\Controllers\ChandleryController::class, 'delete_chandlery'])->name('delete-chandlery');

    /*
    * Web routes for Club House
    */
    Route::get('/club-house', [App\Http\Controllers\ClubHouseController::class, 'index'])->name('club-house');
    Route::get('/add-club-house', [App\Http\Controllers\ClubHouseController::class, 'add_club_house'])->name('add-club-house');
    Route::post('/save-club-house', [App\Http\Controllers\ClubHouseController::class, 'save_club_house'])->name('save-club-house');
    Route::get('/club-house/edit/{id}', [App\Http\Controllers\ClubHouseController::class, 'edit_club_house'])->name('club-house-edit');
    Route::post('/update-club-house/{id}', [App\Http\Controllers\ClubHouseController::class, 'update_club_house'])->name('update-club-house');
    Route::get('/club-house/delete/{id}', [App\Http\Controllers\ClubHouseController::class, 'delete_club_house'])->name('delete-club-house');
    Route::post('/club-house/add-moderator/{id}', [App\Http\Controllers\ClubHouseController::class, 'addModerator'])->name('add-moderator');
    Route::get('/club-house/get-moderators/{id}', [App\Http\Controllers\ClubHouseController::class, 'getModerators'])->name('get-moderators');
    Route::get('/club-house/delete-moderator/{id}', [App\Http\Controllers\ClubHouseController::class, 'deleteModerator'])->name('delete-moderator');

    /*
    * Web routes for Site-pages
    */
    Route::get('/site-pages', [App\Http\Controllers\SitePageController::class, 'index'])->name('site-pages');
    Route::get('/add-site-pages', [App\Http\Controllers\SitePageController::class, 'add_site_page'])->name('add-site-pages');
    Route::post('/save-site-page', [App\Http\Controllers\SitePageController::class, 'save_site_page'])->name('save-site-page');
    Route::get('/site-page/edit/{id}', [App\Http\Controllers\SitePageController::class, 'edit_site_page'])->name('site-page-edit');
    Route::post('/update-site-page/{id}', [App\Http\Controllers\SitePageController::class, 'update_site_page'])->name('update-site-page');
    Route::get('/site-page/delete/{id}', [App\Http\Controllers\SitePageController::class, 'delete_site_page'])->name('delete-site-page');
    Route::get('/other-pages', [App\Http\Controllers\SitePageController::class, 'other_pages'])->name('other-pages');
    Route::get('/other-pages/edit/{id}', [App\Http\Controllers\SitePageController::class, 'edit_other_page'])->name('other-page-edit');
    Route::post('/update-other-page', [App\Http\Controllers\SitePageController::class, 'update_other_page'])->name('update-other-page');
    Route::get('/home-page', [App\Http\Controllers\SitePageController::class, 'home_page'])->name('home-page');
    Route::get('/home-page-edit/{id}', [App\Http\Controllers\SitePageController::class, 'edit_home_page_cms'])->name('home-page-edit');
    Route::post('/update-home-page-cms/{id}', [App\Http\Controllers\SitePageController::class, 'update_home_page_cms'])->name('update-home-page-cms');
    Route::get('/add-home-page-section', [App\Http\Controllers\SitePageController::class, 'addHomePage'])->name('add-home-page-section');
    Route::post('/save-home-page', [App\Http\Controllers\SitePageController::class, 'saveHomepage'])->name('save-home-page');
    Route::get('/home-page/delete/{id}', [App\Http\Controllers\SitePageController::class, 'deletehomePage'])->name('delete-home-page');
    Route::get('/site-page/delete-home-video/{id}', [App\Http\Controllers\SitePageController::class, 'deletehomeVideo'])->name('delete-home-video');
    Route::get('/reach-membership-page', [App\Http\Controllers\SitePageController::class, 'reachMembershipPageList'])->name('reach-membership-page');
    Route::get('/add-reach-membership-pages', [App\Http\Controllers\SitePageController::class, 'addReachMembershipPage'])->name('add-reach-membership-pages');
    Route::post('/save-reach-membership-page', [App\Http\Controllers\SitePageController::class, 'saveReachMembershippage'])->name('save-reach-membership-page');
    Route::get('/membership-page-edit/{id}', [App\Http\Controllers\SitePageController::class, 'edit_membership_page'])->name('membership-page-edit');
    Route::get('/membership-page/delete/{id}', [App\Http\Controllers\SitePageController::class, 'deleteMemberShipPage'])->name('delete-membership-page');
    Route::post('/update-membership-page/{id}', [App\Http\Controllers\SitePageController::class, 'update_membership_page'])->name('update-membership-page');

    /*
    * Web routes for App Home
    */
    Route::get('/app-pages', [App\Http\Controllers\SitePageController::class, 'appHome'])->name('app-pages');
    Route::get('/add-app-pages', [App\Http\Controllers\SitePageController::class, 'addappPage'])->name('add-app-pages');
    Route::post('/save-app-page', [App\Http\Controllers\SitePageController::class, 'saveApppage'])->name('save-app-page');
    Route::get('/app-page/edit/{id}', [App\Http\Controllers\SitePageController::class, 'editappPage'])->name('app-page-edit');
    Route::post('/update-app-page/{id}', [App\Http\Controllers\SitePageController::class, 'updateappPage'])->name('update-app-page');
    Route::get('/app-page/delete/{id}', [App\Http\Controllers\SitePageController::class, 'deleteappPage'])->name('delete-app-page');
    Route::get('/app-page/delete-app-home-video/{id}', [App\Http\Controllers\SitePageController::class, 'deleteapphomeVideo'])->name('delete-app-home-video');

    /*
    * Web routes for Settings
    */

    Route::get('/job-role', [App\Http\Controllers\SettingsController::class, 'role_list'])->name('job-role');
    Route::get('/add-job-role', [App\Http\Controllers\SettingsController::class, 'role_add'])->name('add-job-role');
    Route::post('/save-job-role', [App\Http\Controllers\SettingsController::class, 'save_job_role'])->name('save-job-role');
    Route::get('/job-role/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_job_role'])->name('job-role-edit');
    Route::post('/update-job-role/{id}', [App\Http\Controllers\SettingsController::class, 'update_job_role'])->name('update-job-role');
    Route::get('/job-role/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_job_role'])->name('delete-job-role');

    Route::get('/boat-type', [App\Http\Controllers\SettingsController::class, 'boat_type_list'])->name('boat-type');
    Route::get('/add-boat-type', [App\Http\Controllers\SettingsController::class, 'boat_type_add'])->name('add-boat-type');
    Route::post('/save-boat-type', [App\Http\Controllers\SettingsController::class, 'save_boat_type'])->name('save-boat-type');
    Route::get('/boat-type/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_boat_type'])->name('boat-type-edit');
    Route::post('/update-boat-type/{id}', [App\Http\Controllers\SettingsController::class, 'update_boat_type'])->name('update-boat-type');
    Route::get('/boat-type/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_boat_type'])->name('delete-boat-type');

    Route::get('/job-duration', [App\Http\Controllers\SettingsController::class, 'duration_list'])->name('job-duration');
    Route::get('/add-job-duration', [App\Http\Controllers\SettingsController::class, 'duration_add'])->name('add-job-duration');
    Route::post('/save-job-duration', [App\Http\Controllers\SettingsController::class, 'save_job_duration'])->name('save-job-duration');
    Route::get('/job-duration/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_job_duration'])->name('job-duration-edit');
    Route::post('/update-job-duration/{id}', [App\Http\Controllers\SettingsController::class, 'update_job_duration'])->name('update-job-duration');
    Route::get('/job-duration/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_job_duration'])->name('delete-job-duration');

    Route::get('/boat-location', [App\Http\Controllers\SettingsController::class, 'boat_location_list'])->name('boat-location');
    Route::get('/add-boat-location', [App\Http\Controllers\SettingsController::class, 'boat_location_add'])->name('add-boat-location');
    Route::post('/save-boat-location', [App\Http\Controllers\SettingsController::class, 'save_boat_location'])->name('save-boat-location');
    Route::get('/boat-location/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_boat_location'])->name('boat-location-edit');
    Route::post('/update-boat-location/{id}', [App\Http\Controllers\SettingsController::class, 'update_boat_location'])->name('update-boat-location');
    Route::get('/boat-location/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_boat_location'])->name('delete-boat-location');

    Route::get('/languages', [App\Http\Controllers\SettingsController::class, 'languages_list'])->name('languages');
    Route::get('/add-languages', [App\Http\Controllers\SettingsController::class, 'languages_add'])->name('add-languages');
    Route::post('/save-languages', [App\Http\Controllers\SettingsController::class, 'save_languages'])->name('save-languages');
    Route::get('/languages/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_language'])->name('language-edit');
    Route::post('/update-language/{id}', [App\Http\Controllers\SettingsController::class, 'update_language'])->name('update-language');
    Route::get('/languages/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_language'])->name('delete-language');

    Route::get('/qualifications', [App\Http\Controllers\SettingsController::class, 'qualifications_list'])->name('qualifications');
    Route::get('/add-qualifications', [App\Http\Controllers\SettingsController::class, 'qualifications_add'])->name('add-qualifications');
    Route::post('/save-qualifications', [App\Http\Controllers\SettingsController::class, 'save_qualifications'])->name('save-qualifications');
    Route::get('/qualifications/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_qualification'])->name('qualification-edit');
    Route::post('/update-qualification/{id}', [App\Http\Controllers\SettingsController::class, 'update_qualification'])->name('update-qualification');
    Route::get('/qualifications/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_qualification'])->name('delete-qualification');

    Route::get('/experience', [App\Http\Controllers\SettingsController::class, 'experience_list'])->name('experience');
    Route::get('/add-experience', [App\Http\Controllers\SettingsController::class, 'experience_add'])->name('add-experience');
    Route::post('/save-experience', [App\Http\Controllers\SettingsController::class, 'save_experience'])->name('save-experience');
    Route::get('/experience/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_experience'])->name('experience-edit');
    Route::post('/update-experience/{id}', [App\Http\Controllers\SettingsController::class, 'update_experience'])->name('update-experience');
    Route::get('/experience/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_experience'])->name('delete-experience');

    Route::get('/availability', [App\Http\Controllers\SettingsController::class, 'availability_list'])->name('availability');
    Route::get('/add-availability', [App\Http\Controllers\SettingsController::class, 'availability_add'])->name('add-availability');
    Route::post('/save-availability', [App\Http\Controllers\SettingsController::class, 'save_availability'])->name('save-availability');
    Route::get('/availability/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_availability'])->name('availability-edit');
    Route::post('/update-availability/{id}', [App\Http\Controllers\SettingsController::class, 'update_availability'])->name('update-availability');
    Route::get('/availability/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_availability'])->name('delete-availability');

    Route::get('/positions', [App\Http\Controllers\SettingsController::class, 'positions_list'])->name('positions');
    Route::get('/add-positions', [App\Http\Controllers\SettingsController::class, 'positions_add'])->name('add-positions');
    Route::post('/save-positions', [App\Http\Controllers\SettingsController::class, 'save_positions'])->name('save-positions');
    Route::get('/positions/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_positions'])->name('positions-edit');
    Route::post('/update-positions/{id}', [App\Http\Controllers\SettingsController::class, 'update_positions'])->name('update-positions');
    Route::get('/positions/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_positions'])->name('delete-positions');

    Route::get('/salary-expectations', [App\Http\Controllers\SettingsController::class, 'salary_expectations_list'])->name('salary-expectations');
    Route::get('/add-salary-expectations', [App\Http\Controllers\SettingsController::class, 'salary_expectations_add'])->name('add-salary-expectations');
    Route::post('/save-salary-expectations', [App\Http\Controllers\SettingsController::class, 'save_salary_expectations'])->name('save-salary-expectations');
    Route::get('/salary-expectations/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_salary_expectations'])->name('salary-expectations-edit');
    Route::post('/update-salary-expectations/{id}', [App\Http\Controllers\SettingsController::class, 'update_salary_expectations'])->name('update-salary-expectations');
    Route::get('/salary-expectations/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_salary_expectations'])->name('delete-salary-expectations');
    Route::get('/reported-list', [App\Http\Controllers\SettingsController::class, 'reported_list'])->name('reported_list');
    Route::get('/get-last-messages/{receiverId}/{senderId}', [App\Http\Controllers\SettingsController::class, 'getLastMessages']);


    Route::get('/category', [App\Http\Controllers\SettingsController::class, 'category_list'])->name('category');
    Route::get('/add-category', [App\Http\Controllers\SettingsController::class, 'category_add'])->name('add-category');
    Route::post('/save-category', [App\Http\Controllers\SettingsController::class, 'save_category'])->name('save-category');
    Route::get('/category/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_category'])->name('category-edit');
    Route::post('/update-category/{id}', [App\Http\Controllers\SettingsController::class, 'update_category'])->name('update-category');
    Route::get('/category/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_category'])->name('delete-category');

    Route::get('/vessels', [App\Http\Controllers\SettingsController::class, 'vessel_list'])->name('vessels');
    Route::get('/add-vessel', [App\Http\Controllers\SettingsController::class, 'vessel_add'])->name('add-vessel');
    Route::post('/save-vessel', [App\Http\Controllers\SettingsController::class, 'save_vessel'])->name('save-vessel');
    Route::get('/vessels/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_vessel'])->name('vessel-edit');
    Route::post('/update-vessel/{id}', [App\Http\Controllers\SettingsController::class, 'update_vessel'])->name('update-vessel');
    Route::get('/vessels/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_vessel'])->name('delete-vessel');

    Route::get('/countries', [App\Http\Controllers\SettingsController::class, 'countries_list'])->name('countries');
    Route::post('/update-countries', [App\Http\Controllers\SettingsController::class, 'updateCountryStatus'])->name('update-countries');

    Route::get('/visa', [App\Http\Controllers\SettingsController::class, 'visa_list'])->name('visa');
    Route::get('/add-visa', [App\Http\Controllers\SettingsController::class, 'visa_add'])->name('add-visa');
    Route::post('/save-visa', [App\Http\Controllers\SettingsController::class, 'save_visa'])->name('save-visa');
    Route::get('/visa/edit/{id}', [App\Http\Controllers\SettingsController::class, 'edit_visa'])->name('visa-edit');
    Route::post('/update-visa/{id}', [App\Http\Controllers\SettingsController::class, 'update_visa'])->name('update-visa');
    Route::get('/visa/delete/{id}', [App\Http\Controllers\SettingsController::class, 'delete_visa'])->name('delete-visa');
    /*
    * Web routes for Master Settings
    */
    Route::get('/settings/edit', [App\Http\Controllers\MasterSettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings/update', [App\Http\Controllers\MasterSettingsController::class, 'update'])->name('settings.update');

    Route::get('/transactions/payments', [App\Http\Controllers\TransactionsController::class, 'index'])->name('payments');
    Route::get('/transactions/membership', [App\Http\Controllers\TransactionsController::class, 'membership'])->name('membership');
    Route::get('/transactions/transfers', [App\Http\Controllers\TransactionsController::class, 'transfers'])->name('transfers');
    Route::get('/transactions/transaction_history', [App\Http\Controllers\TransactionsController::class, 'transaction_history'])->name('transaction_history');
    Route::get('/transactions/details/{id}', [App\Http\Controllers\TransactionsController::class, 'details']);


    Route::get('/settings/stripe/{id}', [App\Http\Controllers\MasterSettingsController::class, 'stripe'])->name('settings.stripe');
    Route::get('/settings/connect-stripe', [App\Http\Controllers\MasterSettingsController::class, 'connectStripe'])->name('connect-stripe');
    Route::post('/create-connected-account', [App\Http\Controllers\MasterSettingsController::class, 'createConnectedAccount'])->name('create-connected-account');
    Route::get('/settings/disconnect/{id}', [App\Http\Controllers\MasterSettingsController::class, 'disconnectStripe'])->name('settings.disconnect');

    /*
    * Web routes for Stripe Payments
    */
    Route::get('/book-now', [App\Http\Controllers\PaymentController::class, 'showPaymentForm'])->name('book.now');
    Route::post('/process-payment', [App\Http\Controllers\PaymentController::class, 'processPayment'])->name('process.payment');
    Route::get('/payment-success', [App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('payment.success');

    Route::post('/do-payment-transfer', [App\Http\Controllers\PaymentController::class, 'doPaymentTransfer'])->name('do-payment-transfer');
    Route::post('/do-payment-card-details', [App\Http\Controllers\PaymentController::class, 'doPaymentCardDetails'])->name('do-payment-card-details');

    Route::get('/add-caller-id', [App\Http\Controllers\SpecialistController::class, 'addCallerId'])->name('add-caller-id');

    /*
    * Web routes for Email Templates
    */
    Route::get('/templates', [App\Http\Controllers\TemplateController::class, 'index'])->name('templates');
    Route::get('/templates/add', [App\Http\Controllers\TemplateController::class, 'add_template'])->name('add-template');
    Route::post('/save-template', [App\Http\Controllers\TemplateController::class, 'save_template'])->name('save-template');
    Route::get('/templates/edit/{id}', [App\Http\Controllers\TemplateController::class, 'edit_template'])->name('edit-template');
    Route::get('/announcement_list', [App\Http\Controllers\AnnouncementController::class, 'announcement_list'])->name('announcement_list');
    Route::get('/add_announcement', [App\Http\Controllers\AnnouncementController::class, 'add_announcement'])->name('add_announcement');
    Route::post('/save_announcement', [App\Http\Controllers\AnnouncementController::class, 'save_announcement'])->name('save_announcement');
    Route::get('/announcement/delete/{id}', [App\Http\Controllers\AnnouncementController::class, 'delete_announcement'])->name('delete_announcement');
});

Route::middleware('auth:sanctum')->group(function () {});
