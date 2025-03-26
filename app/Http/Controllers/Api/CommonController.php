<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\ReachMember;
use App\Models\ReachContactUs;
use App\Models\ReachCountry;
use App\Models\ReachLanguages;
use App\Models\ReachQualifications;
use App\Models\ReachExperience;
use App\Models\ReachAvailability;
use App\Models\ReachSalaryExpectations;
use App\Models\ReachPositions;
use App\Models\ReachSitePage;
use App\Models\ReachPartner;
use App\Models\ReachChandlery;
use App\Models\ReachClubHouse;
use App\Models\MasterSetting;
use App\Models\ChatReportMembers;
use App\Models\ChatRequests;
use App\Models\ReachEmailTemplate;

use App\Libraries\MailchimpService;
use App\Models\ReachHomeCms;
use App\Models\ReachMembershipPage;
use App\Models\ReferralTypes;
use App\Services\CurrencyService;
use App\Models\ReachChandleryCouponCodes;

class CommonController extends Controller
{

	/**
	 * Handle the contact form submission.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\JsonResponse
	 */

	public function contactUs(Request $request)
	{
		try {

			$requestData = $request->all();

			// Replace keys in the request data
			$data = $this->replaceKeys($requestData, 'members_', 'get_in_touch_');

			$user_name = $data['get_in_touch_fname'] . " " . $data['get_in_touch_lname'];
			//$phone_number = $data['get_in_touch_phone_code']." ".$data['get_in_touch_phone_number'];

			$data['get_in_touch_message'] = $requestData['message'];
			unset($data['message']);

			// Create a new record in the ReachContactUs model with the modified data
			$contactUs = ReachContactUs::create($data);

			// Send an email notification
			$emailTemplate = ReachEmailTemplate::where('template_type', 'get_in_touch')->first();

			// Prepare Email Subject and Body
			$subject = $emailTemplate->template_subject;
			$content = $emailTemplate->template_message;
			$tags = explode(",", $emailTemplate->template_tags);
			$replace = [$user_name, $data['get_in_touch_email'], $data['get_in_touch_message']];
			$body = str_replace($tags, $replace, $content);

			// Send Email to Admin
			$to = $emailTemplate->template_to_address;

			$mailchimpService = new MailchimpService();
			$mailchimpService->sendTemplateEmail(
				$to,
				$body,
				$subject,
				'info@reach.boats'
			);

			return response()->json(['success' => true, 'message' => 'Your message has been sent'], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while processing your request',
				'error' => $e->getMessage()
			], 500);
		}
	}

	private function replaceKeys($array, $search, $replace)
	{
		$newArray = [];
		foreach ($array as $key => $value) {
			$newKey = str_replace($search, $replace, $key);
			$newArray[$newKey] = $value;
		}
		return $newArray;
	}

	/**
	 * Retrieve dial codes for countries.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\JsonResponse
	 */

	public function getDialCode(Request $request)
	{
		try {
			// Try to retrieve data from cache. If not found, execute the closure and cache the result.
			$countries = ReachCountry::select('id', DB::raw("CONCAT('+', country_phonecode) AS country_phonecode"), 'country_name')->where('country_status', 'A')->get()->pluck(null, 'id')->toArray();

			// Return success response with the cached data
			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $countries
			], 200);
		} catch (\Exception $e) {
			// Handle exceptions if any occur
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage() // Include the error message in the response
			], 500); // Set HTTP status code to 500 for internal server error
		}
	}

	/**
	 * Retrieve list of countries.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\JsonResponse
	 */

	public function getCountries(Request $request)
	{
		try {
			// Try to retrieve data from cache. If not found, execute the closure and cache the result.
			$countries = ReachCountry::select('id', 'country_iso', 'country_name', DB::raw("CONCAT('+', country_phonecode) AS country_phonecode"))->where('country_status', 'A')->get()->toArray();

			// Return success response with the cached data
			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $countries
			], 200);
		} catch (\Exception $e) {
			// Handle exceptions if any occur
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage() // Include the error message in the response
			], 500); // Set HTTP status code to 500 for internal server error
		}
	}

	public function getLanguages()
	{
		try {

			$languages = ReachLanguages::where('language_status', 'A')->pluck('lang_id', 'language_name')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $languages
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getQualifications()
	{
		try {

			$qualifications = ReachQualifications::where('qualification_status', 'A')->pluck('qualification_id', 'qualification_name')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $qualifications
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getExperience()
	{
		try {

			$experience = ReachExperience::where('experience_status', 'A')->pluck('experience_id', 'experience_name')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $experience
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getAvailability()
	{
		try {

			$availability = ReachAvailability::where('availability_status', 'A')->pluck('availability_id', 'availability_name')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $availability
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getPositions()
	{
		try {

			$positions = ReachPositions::where('position_status', 'A')->pluck('position_id', 'position_name')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $positions
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getSalaryExpectations()
	{
		try {

			$expectations = ReachSalaryExpectations::where('expectation_status', 'A')->pluck('expectation_id', 'expectation_name')->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $expectations
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getCmsContents(Request $request)
	{
		try {

			$slug = $request->query('slug');
			$sitepage = ReachSitePage::where('site_page_slug', $slug)
				->where('site_page_status', 'A')
				->get()
				->toArray();
			$cmsPage = [];
			foreach ($sitepage as $page) {
				$cmsPage['pageHeader'] = $page['site_page_header'];
				$cmsPage['pageDetails'] = $page['site_page_details'];
				$cmsPage['pageImage'] = asset('storage/' . $page['site_page_images']);
				if ($page['site_page_slug'] == 'discover-reach') {
					$cmsPage['leftsidecontent'] = $page['left_side_content'];
				}
				if (($page['site_page_slug'] == 'joinMembership') || ($page['site_page_slug'] == 'freeMembership') || ($page['site_page_slug'] == 'member-signup')) {
					$cmsPage['pageDetails'] = strip_tags($page['site_page_details']);
				}
				if ($page['site_page_slug'] == 'cruz_jobs') {
					$cmsPage['cruz_title'] = $page['cruz_title'];
					$cmsPage['cruz_description'] = $page['cruz_description'];
				}
			}

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $cmsPage
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function ourPartners()
	{
		try {

			$partners = ReachPartner::where('partner_status', 'A')
				->select('id', 'partner_name', 'partner_details', 'partner_side_image', 'partner_side_image_mob', 'partner_logo', 'partner_video', 'video_file_type', 'partner_video_title', 'partner_video_thumb', 'partner_web_url', 'is_chandlery', 'partner_side_video', 'partner_side_video_mob')
				->orderBy('partner_display_order', 'asc')
				->get()
				->map(function ($item) {
					$item['partner_details'] = html_entity_decode(strip_tags($item['partner_details']));
					return $item;
				})
				->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $partners,
				'filePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function chandlery(Request $request)
	{
		$member = $request->user();
		$member_id = $member->id;
		try {

			$chandlery = ReachChandlery::where('chandlery_status', 'A')
				->select('id', 'chandlery_name', 'chandlery_description', 'chandlery_discount', 'chandlery_website', 'chandlery_image', 'chandlery_logo', 'show_coupon_code')
				->orderBy('chandlery_order', 'asc')
				->get()
				->map(function ($item) use ($member_id) {
					$coupons = ReachChandleryCouponCodes::where('chandlery_id', $item['id'])
						->where('member_id', $member_id)
						->first(['coupon_code']);
					return [
						'id' => $item['id'],
						'chandlery_name' => $item['chandlery_name'],
						'chandlery_coupon_code' => $coupons ? $coupons->coupon_code : null,
						'chandlery_discount' => (floor($item['chandlery_discount']) == $item['chandlery_discount']) ? number_format($item['chandlery_discount'], 0) : $item['chandlery_discount'],
						'chandlery_website' => $item['chandlery_website'],
						'chandlery_logo' => $item['chandlery_logo'],
						'chandlery_image' => $item['chandlery_image'],

						'chandlery_description' => html_entity_decode(strip_tags($item['chandlery_description'])),
						'show_coupon_code' => $item['show_coupon_code'],
					];
				})
				->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $chandlery,
				'filePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function clubHouse()
	{
		try {

			$clubHouse = ReachClubHouse::where('club_status', 'A')
				->select('id', 'club_name', 'club_short_desc', 'club_image', 'club_button_name', 'club_image_mob', 'club_image_thumb')
				->orderBy('club_order', 'asc')
				->get()
				->map(function ($item) {
					$item['club_short_desc'] = html_entity_decode(strip_tags($item['club_short_desc']));
					return $item;
				})
				->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $clubHouse,
				'filePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function membershipFee()
	{
		try {

			$feeSettings = MasterSetting::select(
				'specialist_booking_fee',
				'member_cancel_fee',
				'full_membership_fee',
				'monthly_membership_fee',
				'full_membership_fee_euro',
				'full_membership_fee_dollar',
				'monthly_membership_fee_euro',
				'monthly_membership_fee_dollar'
			)->find(1);
			if ($feeSettings) {
				$feeSettings->monthly_membership_fee = number_format($feeSettings->monthly_membership_fee, 2);
				$feeSettings->monthly_membership_fee_euro = number_format($feeSettings->monthly_membership_fee_euro, 2);
				$feeSettings->monthly_membership_fee_dollar = number_format($feeSettings->monthly_membership_fee_dollar, 2);
			}

			return response()->json([
				'success' => true,
				'data' => $feeSettings,
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function chandleryOld(Request $request)
	{
		$member = $request->user();
		$member_id = $member->id;
		try {

			$chandlery = ReachPartner::where('partner_status', 'A')
				->where('is_chandlery', 'Y')
				->select('id', 'partner_name', 'partner_logo', 'partner_description', 'partner_coupon_code', 'partner_discount', 'partner_web_url', 'partner_cover_image', 'partner_cover_image_mob', 'partner_side_image', 'partner_side_image_mob', 'show_coupon_code')
				->orderBy('partner_display_order', 'asc')
				->get()
				->map(function ($item) use ($member_id) {
					$coupons = ReachChandleryCouponCodes::where('chandlery_id', $item['id'])
						->where('member_id', $member_id)
						->first(['coupon_code']);
					return [
						'id' => $item['id'],
						'chandlery_name' => $item['partner_name'],
						'chandlery_coupon_code' => $coupons ? $coupons->coupon_code : null,
						'chandlery_discount' => (floor($item['partner_discount']) == $item['partner_discount']) ? number_format($item['partner_discount'], 0) : $item['partner_discount'],
						'chandlery_website' => $item['partner_web_url'],
						'chandlery_logo' => $item['partner_logo'],
						'chandlery_image' => $item['partner_cover_image'],
						'chandlery_image_mob' => $item['partner_cover_image_mob'],
						'chandlery_side_image' => $item['partner_side_image'],
						'chandlery_side_image_mob' => $item['partner_side_image_mob'],
						'chandlery_description' => html_entity_decode(strip_tags($item['partner_description'])),
						'show_coupon_code' => $item['show_coupon_code'],
					];
				})
				->toArray();

			return response()->json([
				'success' => true,
				'message' => 'OK',
				'data' => $chandlery,
				'filePath' => url('storage')
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function chatReportMember(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'reported_member_id' => 'required',
			'reported_by_member_id' => 'required',
		], [
			'reported_member_id.required' => 'The report member id is required.',
			'reported_by_member_id.required' => 'The report member id is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			$report = ChatReportMembers::create($requestData);

			$arr_data['sender_id'] = $requestData['reported_member_id'];
			$arr_data['receiver_id'] = $requestData['reported_by_member_id'];
			$arr_data['status'] = 3;
			$request = ChatRequests::create($arr_data);

			return response()->json(['success' => true, 'message' => 'Report member successfully'], 200);
		}
	}

	public function validateReferralCode(Request $request)
	{
		$requestData = $request->all();

		$validator = Validator::make($requestData, [
			'referral_code' => 'required',
		], [
			'referral_code.required' => 'The referral code is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			$referringUser = ReachMember::whereRaw('BINARY referral_code = ?', [$request->referral_code])->where('members_type', 'M')
				->first();

			if ($referringUser) {
				return response()->json([
					'success' => true,
					'message' => 'Referral code is valid.',
				], 200);
			} else {
				return response()->json([
					'success' => false,
					'message' => 'Referral code is invalid.',
				], 404);
			}
		}
	}

	public function getAppHomeDetails()
	{
		try {
			$appHomeData = ReachSitePage::where('site_page_type', 'A')
				->where('site_page_status', 'A')
				->orderBy('order', 'asc')
				->get()
				->map(function ($item) {
					return [
						'slug' => $item['site_page_slug'],
						'title' => $item['site_page_header'],
						'image' => $item['site_page_images'],
						'video' => $item['site_page_video'],
						'displayorder' => $item['order'],
						'details' => html_entity_decode(strip_tags($item['site_page_details'])),
						'chandlery_percentage' => $item['site_chandlery_percentage'],
						'chandlery_coupon' => $item['site_chandlery_coupon'],
						'chandlery_url' => $item['site_chandlery_url'],
						'chandlery_text' => $item['site_chandlery_text'],
						'chandlery_logo' => $item['site_chandlery_logo'],
					];
				})
				->toArray();

			return response()->json(['success' => true, 'message' => 'OK', 'data' => $appHomeData, 'filepath' => url('storage')], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}
	public function getSiteHomeDetails()
	{
		try {
			$HomeData = ReachHomeCms::where('home_page_section_status', 'A')
				->orderBy('order', 'asc')
				->get()
				->map(function ($item) {
					return [
						'home_page_section_header' => $item['home_page_section_header'],
						'home_page_section_type' => $item['home_page_section_type'],
						'home_page_section_images' => $item['home_page_section_images'],
						'home_page_section_details' => html_entity_decode(strip_tags($item['home_page_section_details'])),
						'home_page_section_button' => $item['home_page_section_button'],
						'home_page_section_button_link' => $item['home_page_section_button_link'],
						'home_page_video' => $item['home_page_video'] ? $item['home_page_video'] : null,
						'display_order' => $item['order'],

					];
				})
				->toArray();

			return response()->json(['success' => true, 'message' => 'OK', 'data' => $HomeData, 'filepath' => url('storage')], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function join_reach(Request $request)
	{

		try {

			$membershippage = ReachMembershipPage::where('status', 'A')
				->orderBy('id', 'asc')
				//->take(2)
				->get()
				->map(function ($item) {
					return [
						'membership_title' => $item['membership_title'],
						'membership_description' => preg_replace('/\s*style="[^"]*"/i', '', $item['membership_description']),
						'membership_button' => $item['membership_button'],
					];
				})
				->toArray();

			return response()->json(['success' => true, 'message' => 'OK', 'data' => $membershippage], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function referral_discount(Request $request)
	{
		$requestData = $request->all();
		$feeSettingsNew = [];
		$validator = Validator::make($requestData, [
			'referral_code' => 'required',
		], [
			'referral_code.required' => 'The referral code is required.',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()], 422);
		} else {

			$referringUser = ReachMember::where('referral_code', $request->referral_code)
				->where('members_type', 'M')
				->first();
			$feeSettings = MasterSetting::select(
				'full_membership_fee',
				'monthly_membership_fee',
				'full_membership_fee_euro',
				'full_membership_fee_dollar',
				'monthly_membership_fee_euro',
				'monthly_membership_fee_dollar'
			)->find(1);
			if ($feeSettings) {

				if ($request->currency === 'EUR') {
					$monthlyFee = $feeSettings->monthly_membership_fee_euro;
					$fullFee = $feeSettings->full_membership_fee_euro;
				} else if ($request->currency === 'USD') {
					$monthlyFee = $feeSettings->monthly_membership_fee_dollar;
					$fullFee = $feeSettings->full_membership_fee_dollar;
				} else {
					$monthlyFee = $feeSettings->monthly_membership_fee;
					$fullFee = $feeSettings->full_membership_fee;
				}

				if (empty($referringUser)) {
					$referral_rate = ReferralTypes::first()->referral_rate;
					$feeSettingsNew['referal_percentage'] = $referral_rate;
				}
				if ($referringUser && isset($referringUser->referral_rate)) {
					$discountPercentage = $referringUser->referral_rate;
					$discountedMonthlyFee = $monthlyFee * (1 - $discountPercentage / 100);
					$discountedFullFee = $fullFee * (1 - $discountPercentage / 100);
					$feeSettingsNew['monthly_membership_fee'] = number_format($discountedMonthlyFee, 2);
					$feeSettingsNew['full_membership_fee'] = number_format($discountedFullFee, 2);
					$feeSettingsNew['referal_percentage'] = $referringUser->referral_rate;
				}


				return response()->json([
					'success' => true,
					'data' => $feeSettingsNew,
				], 200);
			} else {
				return response()->json([
					'success' => false,
					'message' => 'Referral code is invalid.',
				], 404);
			}
		}
	}

	public function currencyConvert(Request $request)
	{
		$from = $request->input('from');
		$to = $request->input('to');
		$amount = $request->input('amount');

		$this->currencyService = new CurrencyService();
		$converted_amount = $this->currencyService->convertCurrency($from, $to, $amount);

		if ($converted_amount) {
			return response()->json([
				'success' => true,
				'converted_amount' => $converted_amount,
			]);
		}

		return response()->json([
			'success' => false,
			'error' => 'Unable to convert currency.',
		], 400);
	}

	public function getExchangeRates(Request $request)
	{
		$currency = $request->input('currency');

		$this->currencyService = new CurrencyService();
		$rates = $this->currencyService->getExchangeRates($currency);

		if ($rates) {
			return response()->json([
				'rates' => $rates
			]);
		}

		return response()->json([
			'error' => 'Unable to fetch exchange rates.',
		], 400);
	}

	public function getPaymentInfo(Request $request)
	{
		try {

			$feeSettings = MasterSetting::select(
				'payment_info',
			)->find(1);


			return response()->json([
				'success' => true,
				'data' => $feeSettings,
			], 200);
		} catch (\Exception $e) {

			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}

	public function getCouponCode(Request $request)
	{
		$member = $request->user();
		$member_id = $member->id;
		try {
			$chandlery_id = $request['id'];
			$existingCoupon = ReachChandleryCouponCodes::where('chandlery_id', $chandlery_id)
				->where('member_id', $member_id)
				->first(['coupon_code', 'id']);
			if ($existingCoupon) {
				return response()->json([
					'success' => true,
					'message' => 'You already have a coupon for this chandlery.',
					'data' => $existingCoupon,
				], 200);
			}
			$coupons = ReachChandleryCouponCodes::where('chandlery_id', $chandlery_id)
				->whereNull('member_id')
				->first(['coupon_code', 'id', 'member_id']);

			if (!$coupons) {
				return response()->json([
					'success' => false,
					'message' => 'No available coupons found for this chandlery.',
				], 200);
			}

			$coupons->member_id = $member_id;
			$coupons->save();

			// Return the coupon codes as a response
			return response()->json([
				'success' => true,
				'message' => 'Coupons fetched successfully.',
				'data' => $coupons,
			], 200);
		} catch (\Exception $e) {
			// Return an error response if something goes wrong
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching coupon codes.',
				'error' => $e->getMessage(),
			], 500);
		}
	}
	public function membershipFeaturs(Request $request)
	{
		$requestData = $request->all();
		$status = $requestData['status'];
		try {

			$membershippage = ReachMembershipPage::where('status', $status)
				->orderBy('id', 'asc')
				->first();
			// ->map(function ($item) {
			// 	return [
			// 		'membership_title' => $item['membership_title'],
			// 		'membership_description' => preg_replace('/\s*style="[^"]*"/i', '', $item['membership_description']),
			// 		'membership_button' => $item['membership_button'],
			// 	];
			// })
			//	->toArray();

			return response()->json(['success' => true, 'message' => 'OK', 'data' => $membershippage], 200);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An error occurred while fetching data',
				'error' => $e->getMessage()
			], 500);
		}
	}
}
