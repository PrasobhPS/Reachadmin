<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\ReachMember;
use App\Models\ReachCountry;
use App\Models\ReachEmployer;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachEmployeeMedia;
use App\Models\Specialist_call_schedule;
use App\Models\StripePaymentTransaction;
use App\Libraries\MailchimpService;
use App\Models\ReachEmailTemplate;
use App\Models\ReferralTypes;
use App\Models\MemberActivationLog;
use App\Libraries\StripeConnect;
use Carbon\Carbon;
use MailchimpMarketing\ApiClient;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the list of members.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $query = ReachMember::query();
        $query->leftJoin('reach_referral_types', 'reach_members.referral_type_id', '=', 'reach_referral_types.id');
        // Filtering by member type
        if ($request->has('member_type') && !empty($request->member_type)) {
            $query->where('members_type', $request->member_type);
        }

        // Searching by first name and last name
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('members_fname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_lname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere(DB::raw("CONCAT(members_fname, ' ', members_lname)"), 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_phone', 'LIKE', '%' . $request->search . '%');
            });
        }
        $query->select(
            'reach_members.*',
            'reach_referral_types.referral_type',

        );
        $members = $query->with([
            'memberSchedules',
            'memberTransaction' => function ($query) {
                $query->where('payment_type', 'bookcall');
            }
        ])->orderBy('id', 'desc')->paginate(10);

        $referaalTypes = ReferralTypes::all();
        return view('member/home', [
            'members' => $members,
            'request' => $request,
            'referaalTypes' => $referaalTypes
        ]);
    }

    /**
     * Add member form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function add_member()
    {
        $countries = ReachCountry::where('country_status', 'A')->get();
        return view('member/add_member', ['countries' => $countries]);
    }

    /**
     * Save members to table.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save_member(Request $request)
    {
        $requestData = $request->all();
        $isFreeMember = $request->input('members_type') === 'F';

        if (isset($requestData['members_subscription_end_date']) && !empty($requestData['members_subscription_end_date'])) {
            $requestData['members_subscription_end_date'] = date('Y-m-d', strtotime($requestData['members_subscription_end_date']));
        }

        if (isset($requestData['members_status'])) {
            $requestData['members_status'] = 'A';
        } else {
            $requestData['members_status'] = 'I';
        }

        if (isset($requestData['members_payment_status'])) {
            $requestData['members_payment_status'] = 'A';
        } else {
            $requestData['members_payment_status'] = 'I';
        }

        if ($requestData['members_type'] == "M") {
            $referral_code = ReachMember::generateReferralCode();
            $requestData['referral_code'] = $referral_code;
            $firstReferralType = ReferralTypes::first();
            if ($firstReferralType) {
                $requestData['referral_type_id'] = $firstReferralType->id;
                $requestData['referral_rate'] = $firstReferralType->referral_rate; // Assuming 'rate' is a column in referral_types
            }
        }
        $requestData['members_interest'] = trim(strip_tags($requestData['members_interest']));
        $requestData['members_employment'] = trim(strip_tags($requestData['members_employment']));
        $requestData['members_name_title'] = trim(strip_tags($requestData['members_name_title']));
        $requestData['members_fname'] = trim(strip_tags($requestData['members_fname']));
        $requestData['members_lname'] = trim(strip_tags($requestData['members_lname']));
        $requestData['members_email'] = trim(strip_tags($requestData['members_email']));
        $requestData['members_address'] = trim(strip_tags($requestData['members_address']));
        $requestData['members_town'] = trim(strip_tags($requestData['members_town']));
        // $requestData['members_street'] = trim(strip_tags($requestData['members_street']));
        $requestData['members_region'] = trim(strip_tags($requestData['members_region']));

        $requestData['is_email_verified'] = 1;


        // $validator = Validator::make($requestData, [
        //     'members_fname' => 'required|string',
        //     'members_lname' => 'required|string',
        //     'members_address' => 'required',
        //     //'members_email' => 'required|email|unique:reach_members,members_email',
        //     'members_email' => [
        //         'required',
        //         'email',
        //         Rule::unique('reach_members', 'members_email')->where(function ($query) {
        //             return $query->where('is_deleted', '!=', 'Y');
        //         })
        //     ],
        //     'members_password' => ['required', 'min:8', 'confirmed'],
        //     'members_country' => 'required',
        //     'members_phone' => 'required|string|max:15',

        // ], [
        //     'members_fname.required' => 'The first name is required.',
        //     'members_lname.required' => 'The last name is required.',
        //     'members_address.required' => 'The address is required.',
        //     'members_email.required' => 'The email is required.',
        //     'members_email.email' => 'Please enter a valid email address.',
        //     'members_email.unique' => 'The email address has already been taken.',
        //     'members_password.required' => 'The password is required.',
        //     'members_password.min' => 'The password must be at least :min characters.',
        //     'members_password.confirmed' => 'The password confirmation does not match.',
        //     'members_country.required' => 'The country is required.',
        //     'members_phone.required' => 'The phone is required.',

        // ]);
        $rules = [
            'members_fname' => 'required|string',
            'members_lname' => 'required|string',
            'members_email' => [
                'required',
                'email',
                Rule::unique('reach_members')->where(function ($query) {
                    return $query->where('is_deleted', '!=', 'Y');
                }),
            ],

        ];
        if (!$isFreeMember) {
            $rules = array_merge($rules, [
                'members_address' => 'required',
                'members_country' => 'required',
                'members_phone' => 'required|string|max:15',
            ]);
        }

        if ($request->filled('members_password')) {
            $rules['members_password'] = 'required|min:8';
            $rules['members_password_confirmation'] = 'required|same:members_password';
        }

        $validator = Validator::make($requestData, $rules, [
            'members_fname.required' => 'The first name field is required.',
            'members_lname.required' => 'The last name field is required.',
            'members_address.required' => 'The address field is required.',
            'members_email.required' => 'The email field is required.',
            'members_email.email' => 'Please enter a valid email address.',
            'members_email.unique' => 'The email address has already been taken.',
            'members_country.required' => 'The country field is required.',
            'members_phone.required' => 'The phone field is required.',
            //'members_dob.required' => 'The date of birth field is required.',
            //'members_dob.date' => 'Please enter a valid date for the date of birth.',
            'members_password.required' => 'Please enter a password.',
            'members_password.min' => 'Password must be at least 8 characters long.',
            'members_password_confirmation.required' => 'Please confirm your password.',
            'members_password_confirmation.same' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('members_profile_picture')) {
            $profilePicturePath = $request->file('members_profile_picture')->store('profile-pictures', 'public');
            $requestData['members_profile_picture'] = $profilePicturePath;
        }

        $members_password = $requestData['members_password'];
        $requestData['members_password'] = Hash::make($requestData['members_password']);
        $member = ReachMember::create($requestData);

        // Fetch Email Template
        $emailTemplate = ReachEmailTemplate::where('template_type', 'user_registration')->first();

        // Prepare Email Subject and Body
        $result = "";
        $subject = $emailTemplate->template_subject;
        $body = $emailTemplate->template_message;
        $tags = explode(",", $emailTemplate->template_tags);
        $replace = [$member->members_fname, $member->members_email, $members_password];
        $body = str_replace($tags, $replace, $body);

        // Send Email to user
        $to = $member->members_email;
        $cc = [];
        //$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
        $bcc = [];

        $mailchimpService = new MailchimpService();
        $mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc);
        if ($requestData['members_type'] == "M") {
            $members_type = 'Full-Member';
        } else {
            $members_type = 'Free-Member';
        }
        $this->subscribeToMailchimp($member, ['Reach-Member', $members_type]);
        // Redirect to the member list page
        return redirect()->route('home')->with('success', 'Member created successfully!');
    }

    /**
     * Display the form for editing a specific member.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */

    public function edit_member($id)
    {
        $member = ReachMember::withTrashed()->with(['memberSchedules', 'memberTransaction'])->find($id);
        $countries = ReachCountry::where('country_status', 'A')->get();
        //$stripePaymentTransaction = StripePaymentTransaction::where('member_id', $id)->get();

        return view('member/edit_member', ['member' => $member, 'countries' => $countries, 'id' => $id]);
    }


    /**
     * Update the specified member details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_member(Request $request, $id)
    {

        $requestData = $request->all();
        $member = ReachMember::findOrFail($id);
        $isFreeMember = $request->input('members_type') === 'F';
        // if (!empty($requestData['members_dob'])) {
        //     $requestData['members_dob'] = date('Y-m-d', strtotime($requestData['members_dob']));
        // }
        if (isset($requestData['members_subscription_end_date']) && !empty($requestData['members_subscription_end_date'])) {
            $requestData['members_subscription_end_date'] = date('Y-m-d', strtotime($requestData['members_subscription_end_date']));
        }

        if (isset($requestData['members_status'])) {
            $requestData['members_status'] = 'A';
        } else {
            $requestData['members_status'] = 'I';
        }

        if (isset($requestData['members_payment_status'])) {
            $requestData['members_payment_status'] = 'A';
        } else {
            $requestData['members_payment_status'] = 'I';
        }

        // if (isset($requestData['is_specialist'])) {
        //     $requestData['is_specialist'] = 'Y';
        // } else {
        //     $requestData['is_specialist'] = 'N';
        // }
        $existingSpecialistStatus = $member->is_specialist ?? 'N';
        $requestData['is_specialist'] = isset($requestData['is_specialist']) ? 'Y' : 'N';
        $requestData['members_interest'] = trim(strip_tags($requestData['members_interest']));
        $requestData['members_employment'] = trim(strip_tags($requestData['members_employment']));
        $requestData['members_name_title'] = trim(strip_tags($requestData['members_name_title']));
        $requestData['members_fname'] = trim(strip_tags($requestData['members_fname']));
        $requestData['members_lname'] = trim(strip_tags($requestData['members_lname']));
        $requestData['members_email'] = trim(strip_tags($requestData['members_email']));
        $requestData['members_address'] = trim(strip_tags($requestData['members_address']));
        $requestData['members_town'] = trim(strip_tags($requestData['members_town']));
        // $requestData['members_street'] = trim(strip_tags($requestData['members_street']));
        $requestData['members_region'] = trim(strip_tags($requestData['members_region']));
        $requestData['members_postcode'] = trim(strip_tags($requestData['members_postcode']));
        // Validator rules
        $rules = [
            'members_fname' => 'required|string',
            'members_lname' => 'required|string',
            'members_email' => [
                'required',
                'email',
                Rule::unique('reach_members')->ignore($id)->where(function ($query) {
                    return $query->where('is_deleted', '!=', 'Y');
                }),
            ],

        ];
        if (!$isFreeMember) {
            $rules = array_merge($rules, [
                'members_address' => 'required',
                'members_country' => 'required',
                'members_phone' => 'required|string|max:15',
            ]);
        }

        if ($request->filled('members_password')) {
            $rules['members_password'] = 'required|min:8';
            $rules['members_password_confirmation'] = 'required|same:members_password';
        }

        $validator = Validator::make($requestData, $rules, [
            'members_fname.required' => 'The first name field is required.',
            'members_lname.required' => 'The last name field is required.',
            'members_address.required' => 'The address field is required.',
            'members_email.required' => 'The email field is required.',
            'members_email.email' => 'Please enter a valid email address.',
            'members_email.unique' => 'The email address has already been taken.',
            'members_country.required' => 'The country field is required.',
            'members_phone.required' => 'The phone field is required.',
            //'members_dob.required' => 'The date of birth field is required.',
            //'members_dob.date' => 'Please enter a valid date for the date of birth.',
            'members_password.required' => 'Please enter a password.',
            'members_password.min' => 'Password must be at least 8 characters long.',
            'members_password_confirmation.required' => 'Please confirm your password.',
            'members_password_confirmation.same' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle profile picture upload
        if ($request->hasFile('members_profile_picture')) {
            $profilePicturePath = $request->file('members_profile_picture')->store('profile-pictures', 'public');
            $requestData['members_profile_picture'] = $profilePicturePath;
        }
        if ($requestData['members_type'] == "M") {
            if (empty($member->referral_code)) {
                $referral_code = ReachMember::generateReferralCode();
                $requestData['referral_code'] = $referral_code;
            }
            // $referral_code = ReachMember::generateReferralCode();
            // $requestData['referral_code'] = $referral_code;
            $firstReferralType = ReferralTypes::first();
            if ($firstReferralType) {
                $requestData['referral_type_id'] = $firstReferralType->id;
                $requestData['referral_rate'] = $firstReferralType->referral_rate; // Assuming 'rate' is a column in referral_types
            }
        }


        // if (isset($requestData['members_type']) && $requestData['members_type'] === 'F') {
        //     // Remove authentication token
        //     $member->tokens()->delete();
        // }
        // if (isset($requestData['members_type']) && $requestData['members_type'] === 'M') {
        //     // Remove authentication token
        //     $member->tokens()->delete();
        // }
        // if (isset($requestData['is_specialist']) && $requestData['is_specialist'] !== $member->is_specialist) {
        //     // Remove member token
        //     $member->update(['token' => null]);
        // }
        // Update password only if provided
        if ($request->filled('members_password')) {
            $requestData['members_password'] = Hash::make($requestData['members_password']);
        } else {
            // Remove password from requestData if not provided
            unset($requestData['members_password']);
        }

        if ($requestData['members_type'] == 'F') {
            //$requestData['is_specialist'] = 'N';

            $transaction = StripePaymentTransaction::where('member_id', $id)
                ->where('payment_type', 'membership')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($transaction) {
                $requestData['members_type'] = 'M';
                $subscriptionId = $transaction->stripe_subscription_id;

                // Initialize the Stripe connection
                $this->stripeconnect = new StripeConnect();

                // Retrieve the subscription details to check if it exists
                $subscription = $this->stripeconnect->retrieve_subscription($subscriptionId);

                if ($subscription['status'] === 1 && !empty($subscription['data'])) {

                    $cancellation = $this->stripeconnect->cancel_subscription($subscriptionId);

                    if ($cancellation['status'] === 1) {

                        $arrayData = ['subscription_status' => 'I'];
                        $member->update($arrayData);
                    }
                }
            } else {
                // $requestData['is_specialist'] = 'N';
                $requestData['members_type'] = 'F';
            }
        }
        $memberTypeChanged = $member->members_type != $requestData['members_type'];

        $member->update($requestData);
        if ($memberTypeChanged) {

            \App\Libraries\SocketIO::sendMemberTypeUpdate($id, $requestData['members_type'], $requestData['is_specialist']);
        }

        if (($existingSpecialistStatus !== $requestData['is_specialist']) && ($requestData['members_type'] == 'M')) {
            \App\Libraries\SocketIO::sendExpertUpdate($id, $requestData['is_specialist']);
        }
        if ($requestData['members_status'] == 'I') {
            $member->tokens()->delete();
            // \App\Libraries\SocketIO::sendStatusUpdate($id, $requestData['members_status']);
        }
        return redirect()->route('home')->with('success', 'Member updated successfully!');
    }

    /**
     * Delete member by its Id.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function member_delete($id)
    {
        $member = ReachMember::find($id);

        if ($member) {
            $member->members_status = 'I';
            $member->is_deleted = 'Y';
            $member->deleted_by = 'Admin';
            $member->deleted_at = date('Y-m-d H:i:s');
            $member->save();

            $member->delete();

            // Save activation log
            MemberActivationLog::create([
                'member_id' => $member->id,
                'action_type' => 'D',
                'reason' => 'Member Deleted',
                'created_by' => 'Admin',
            ]);
            $transaction = StripePaymentTransaction::where('member_id', $member->id)
                ->where('payment_type', 'membership')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($transaction) {

                $subscriptionId = $transaction->stripe_subscription_id;

                // Initialize the Stripe connection
                $this->stripeconnect = new StripeConnect();

                // Retrieve the subscription details to check if it exists
                $subscription = $this->stripeconnect->retrieve_subscription($subscriptionId);

                if ($subscription['status'] === 1 && !empty($subscription['data'])) {

                    $cancellation = $this->stripeconnect->cancel_subscription($subscriptionId);

                    if ($cancellation['status'] === 1) {

                        $arrayData = ['subscription_status' => 'I'];
                        $member->update($arrayData);
                    }
                }
            }
            return redirect()->route('home')->with('success', 'Member deleted successfully!');
        } else {
            // If the member doesn't exist, return an error message or handle it as appropriate
            return redirect()->route('home')->with('success', 'Member not found!');
        }
    }

    /**
     * Display the form for viewing a specific member.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function view_member($id)
    {
        $member = ReachMember::find($id);
        // Pass the member data to the view
        return view('member/view_member', ['member' => $member]);
    }

    public function employer_list()
    {
        $employers = ReachEmployer::with('member')->paginate(10);

        return view('member/employer_list', ['employer_list' => $employers]);
    }

    public function view_employer($id)
    {
        $employer = ReachEmployer::with('member')->find($id);

        if (!$employer) {
            return redirect()->back()->with('error', 'Employer not found.');
        }

        return view('member/view_employer', ['employer' => $employer]);
    }

    public function employer_delete($id)
    {
        $employer = ReachEmployer::find($id);

        if ($employer) {
            $employer->delete();
            return redirect()->route('home')->with('success', 'Employer details deleted successfully!');
        } else {
            return redirect()->route('home')->with('success', 'Employer not found!');
        }
    }

    public function employee_list(Request $request)
    {
        $query = ReachEmployeeDetails::query();
        $query->join('reach_members', 'reach_members.id', '=', 'reach_employee_details.member_id');

        // Filtering by member type
        if ($request->has('member_type') && !empty($request->member_type)) {
            $query->where('members_type', $request->member_type);
        }

        // Searching by first name and last name
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('members_fname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_lname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere(DB::raw("CONCAT(members_fname, ' ', members_lname)"), 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        $employee = $query->with('member')->withActiveMembers()->paginate(10);

        return view('member/employee_list', ['employee_list' => $employee]);
    }

    public function view_employee($id)
    {
        $employee = ReachEmployeeDetails::with('member')->find($id);

        $upload_media = ReachEmployeeMedia::where('employee_id', $id)->select('id', 'media_file')->get();

        return view('member/view_employee', ['employee' => $employee, 'upload_media' => $upload_media]);
    }

    public function employee_delete($id)
    {
        $employee = ReachEmployeeDetails::find($id);

        if ($employee) {
            $employee->delete();
            return redirect()->route('home')->with('success', 'Employee details deleted successfully!');
        } else {
            return redirect()->route('home')->with('success', 'Employee not found!');
        }
    }

    public function member_history(Request $request, $id)
    {

        $member = ReachMember::withTrashed()->select('id', 'members_fname', 'members_lname')->find($id);
        $schedule = Specialist_call_schedule::where('member_id', $id)
            ->whereHas('specialist', function ($q) use ($request) {
                if ($request->has('search') && !empty($request->search)) {
                    $q->where('members_fname', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('members_lname', 'LIKE', '%' . $request->search . '%')
                        ->orWhere(DB::raw("CONCAT(members_fname, ' ', members_lname)"), 'LIKE', '%' . $request->search . '%');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('member/member_history', ['member' => $member, 'schedule' => $schedule]);
    }

    public function member_transaction(Request $request, $id)
    {
        $member = ReachMember::withTrashed()->select('id', 'members_fname', 'members_lname')->find($id);

        $query = StripePaymentTransaction::with('member')->where('payment_type', 'bookcall');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        $query->where('member_id', $id);
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        $transactions->appends($request->all());

        return view('member/member_transaction', ['member' => $member, 'transactions' => $transactions, 'filters' => $request->all(), 'i' => ($transactions->currentPage() - 1) * $transactions->perPage() + 1]);
    }

    public function referral_rate($id)
    {
        $rate = ReferralTypes::select('referral_rate as rate')->find($id);
        return response()->json(['rate' => $rate]);
    }

    public function update_referral_type(Request $request)
    {
        $requestData = $request->all();
        ReachMember::where('id', $requestData['member_id'])->update([
            'referral_type_id' => $requestData['referral_type'],
            'referral_rate' => $request->input('referral_rate'),
        ]);
        return response()->json(['success' => true, 'member_id' => $requestData['member_id'], 'message' => 'Member Referral type updated  successfully.']);
    }

    public function deletedMembers(Request $request)
    {
        $query = ReachMember::onlyTrashed();
        $query->leftJoin('reach_referral_types', 'reach_members.referral_type_id', '=', 'reach_referral_types.id');
        // Filtering by member type
        if ($request->has('member_type') && !empty($request->member_type)) {
            $query->where('members_type', $request->member_type);
        }

        // Searching by first name and last name
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('members_fname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_lname', 'LIKE', '%' . $request->search . '%')
                    ->orWhere(DB::raw("CONCAT(members_fname, ' ', members_lname)"), 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_email', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('members_phone', 'LIKE', '%' . $request->search . '%');
            });
        }

        $query->select(
            'reach_members.*',
            'reach_referral_types.referral_type',

        );
        //$query->where('is_deleted', 'Y');

        // Load memberSchedules relationship
        $members = $query->with(['memberSchedules', 'memberTransaction'])->orderBy('id', 'desc')->paginate(10);
        $referaalTypes = ReferralTypes::all();

        return view('member/deleted_list', [
            'members' => $members,
            'request' => $request,
            'referaalTypes' => $referaalTypes
        ]);
    }

    public function member_active(Request $request, $id)
    {
        $member = ReachMember::withTrashed()->find($id);

        if ($member) {

            $requestData['members_status'] = 'A';
            $requestData['is_deleted'] = 'N';
            $requestData['deleted_by'] = null;
            $requestData['deleted_at'] = null;

            $member->restore();

            $member->update($requestData);

            // Save activation log
            MemberActivationLog::create([
                'member_id' => $member->id,
                'action_type' => 'A',
                'reason' => $request->input('reason'),
                'created_by' => 'Admin',
            ]);

            return response()->json(['success' => true, 'message' => 'Member activated successfully!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Member id not found!']);
        }
    }
    public function resendEmail($id)
    {


        $member = ReachMember::where('id', $id)
            ->select('is_email_verified', 'email_verify_token', 'members_fname', 'members_email', 'members_status', 'id')
            ->first();

        if ($member && $member->is_email_verified === 0) {
            $emailTemplate = ReachEmailTemplate::where('template_type', 'verify_email')->first();
            // Prepare Email Subject and Body
            $result = "";
            $subject = $emailTemplate->template_subject;
            $body = $emailTemplate->template_message;
            $tags = explode(",", $emailTemplate->template_tags);


            if (empty($member->email_verify_token)) {
                $member->email_verify_token = Str::random(10);

                ReachMember::where('id', $member->id)
                    ->where(function ($query) {
                        $query->whereNull('email_verify_token')
                            ->orWhere('email_verify_token', '');
                    })
                    ->update(['email_verify_token' =>   $member->email_verify_token]);
            }
            $link = config('site.url') . '/verify-email?token=' . $member->email_verify_token;
            $replace = [$member->members_fname, $link];
            $body = str_replace($tags, $replace, $body);

            // Send Email to user
            $to = $member->members_email;
            $cc = [];
            //$bcc = $emailTemplate->template_bcc_address ? explode(',', $emailTemplate->template_bcc_address) : [];
            $bcc = [];
            $attachments = [];
            $mailchimpService = new MailchimpService();
            $mailchimpService->sendTemplateEmail($to, $body, $subject, NULL, $cc, $bcc, $attachments);
            $currentServerTimezone = Carbon::now('Europe/London');
            $time = $currentServerTimezone->format('Y-m-d H:i:s');

            return redirect()->route('home')->with('success', 'Resend mail sent  successfully!');
        } else {
            return redirect()->route('home')->with('success', 'Email Already Validated');
        }
    }
    private function subscribeToMailchimp($member, $tags = [])
    {
        try {
            $mailchimp = new ApiClient();
            $mailchimp->setConfig([
                'apiKey' => env('MAILCHIMP_API_KEY'),
                'server' => substr(env('MAILCHIMP_API_KEY'), strpos(env('MAILCHIMP_API_KEY'), '-') + 1)
            ]);

            $listId = env('MAILCHIMP_LIST_ID');
            $subscriberHash = md5(strtolower($member->members_email));

            try {
                // Try to get existing member
                $user = $mailchimp->lists->getListMember($listId, $subscriberHash);

                // If we get here, user exists - update their tags
                if ($user->status === 'subscribed') {
                    $mailchimp->lists->updateListMemberTags($listId, $subscriberHash, [
                        'tags' => array_map(function ($tag) {
                            return ['name' => $tag, 'status' => 'active'];
                        }, $tags)
                    ]);

                    return ['success' => true, 'message' => 'Member tags updated successfully'];
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // Handle 404 (member not found) by creating new member
                if ($e->getResponse()->getStatusCode() === 404) {
                    $response = $mailchimp->lists->addListMember($listId, [
                        'email_address' => $member->members_email,
                        'status' => 'subscribed',
                        'merge_fields' => [
                            'FNAME' => $member->members_fname,
                            'LNAME' => $member->members_lname,
                            'ADDRESS' => $member->members_address ?? '',
                            'PHONE' => isset($member->members_phone)
                                ? "+" . $member->members_phone_code . $member->members_phone
                                : '',
                        ],
                        'tags' => $tags
                    ]);

                    return ['success' => true, 'message' => 'New member subscribed successfully'];
                }

                // Re-throw other client errors
                throw $e;
            }
        } catch (\MailchimpMarketing\ApiException $e) {
            \Log::error('Mailchimp API Error: ' . $e->getMessage(), [
                'email' => $member->members_email,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return ['success' => false, 'message' => 'Error subscribing to mailing list'];
        } catch (\Exception $e) {
            \Log::error('Unexpected error in Mailchimp subscription: ' . $e->getMessage(), [
                'email' => $member->members_email,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            return ['success' => false, 'message' => 'Unexpected error in mailing list subscription'];
        }
    }
}
