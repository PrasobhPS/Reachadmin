<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\MasterSetting;

use App\Libraries\StripeConnect;
use App\Models\ReachStripeAccount;
use App\Models\ReachMember;
use App\Models\ReferralTypes;
use App\Models\CurrencyExchangeRates;


class MasterSettingsController extends Controller
{
    public function edit()
    {
        $feeSettings = MasterSetting::find(1);
        $referaalTypes = ReferralTypes::all();
        $currencyExchangeRates = CurrencyExchangeRates::all();
       // print("<PRE>");print_r($currencyExchangeRates);die();

        return view('settings/master_settings', compact('feeSettings','referaalTypes','currencyExchangeRates'));
    }

    public function update(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'specialist_booking_fee' => 'required|numeric',
            'specialist_booking_fee_half_hour' => 'required|numeric',
            'specialist_booking_fee_extra' => 'required|numeric',
            'member_cancel_fee' => 'required|numeric',
            'reach_fee' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } 
        // print("<PRE>");print_r($request->referral_types);die();
     
        foreach ($request->referral_types as $referralData) {
            if (!empty($referralData['id'])) {
                // Update existing record
                ReferralTypes::where('id', $referralData['id'])
                    ->update([
                        'referral_type' => $referralData['type'],
                        'referral_rate' => $referralData['rate'],
                    ]);
            } else {
                // Create new record
                ReferralTypes::create([
                    'referral_type' => $referralData['type'],
                    'referral_rate' => $referralData['rate'],
                ]);
            }
        }
        //update exchnage rate
        foreach ($requestData['exchange_rate_to_usd'] as $id => $rateToUsd) {
            $currencyExchangeRate = CurrencyExchangeRates::find($id);
    
            if ($currencyExchangeRate) {
                $currencyExchangeRate->exchange_rate_to_usd = $rateToUsd;
                $currencyExchangeRate->exchange_rate_to_gbp = $requestData['exchange_rate_to_gbp'][$id];
                $currencyExchangeRate->exchange_rate_to_eur = $requestData['exchange_rate_to_eur'][$id];
                $currencyExchangeRate->save();
            }
        }
        //end update exchange rate


        $setting = MasterSetting::findOrFail(1);
        $setting->update($requestData);

        $updatedMembersReferral = ReachMember::where('members_type', 'M')->update([
            'referral_type_id' => $request->referral_types[0]['id'],
            'referral_rate' => $request->referral_types[0]['rate'],
        ]);
        return redirect()->back()->with('success', 'Fee updated successfully.');
    }

    public function createConnectedAccount(Request $request)
    {
        $requestData = $request->all();

        $validator = Validator::make($requestData, [
            'specialist_id' => 'required',
            'email_id'      => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'address_line1' => 'required',
            'postal_code' => 'required',
            'members_dob' => 'required',
        ], [
            'specialist_id.required' => 'The job role is required.',
            'email_id.required' => 'The email id is required.',
            'email_id.email' => 'The enter a valid email id.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else{

            $dobParts = explode('-', $requestData['members_dob']);
            $requestData['dob_year'] = $dobParts[0];
            $requestData['dob_month'] = $dobParts[1];
            $requestData['dob_day'] = $dobParts[2];

            $requestData['business_name'] = $requestData['first_name'].' '.$requestData['last_name'];
            $requestData['business_url'] = 'https://reach.boats/';

            $this->stripeconnect = new StripeConnect();
            $result = $this->stripeconnect->createConnectedAccount($requestData);

            if ($result['status'] == 1) {

                $account = $result['data'];
                $insert_arr = [
                    'member_id' => $requestData['specialist_id'],
                    'stripe_user_id' => $account->id,
                    'access_token' => $result['account_link'],
                    'scope' => "",
                    'refresh_token' => "",
                    'token_type' => "",
                    'livemode' => 0,
                    'status' => 'A',
                    'created_by' => '1',
                ];

                ReachStripeAccount::create($insert_arr);
                
                return redirect()->back()->with('success', 'Connected account created successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to create connected account: ' . $result['error']);
            }
        }
    }

    public function stripe($id)
    {
        $specialist = ReachMember::find($id);

        $this->stripeconnect = new StripeConnect();

        $connect_url = $this->stripeconnect->getConnectUrl($specialist['id']);
        $stripeAccount = new ReachStripeAccount;
        $connect_stripe_account = $stripeAccount->where('member_id', $specialist['id'])->first();

        $express_access_url = '';
        $express_dashboard_access_url = '';

        if (!empty($connect_stripe_account)) {

            $connected_account_id = $connect_stripe_account->stripe_user_id;

            $redirect_on_logout = route('settings.stripe', ['id' => $specialist['id']]);
            $express_access_url_response = $this->stripeconnect->generate_login_link($connected_account_id, $redirect_on_logout);

            //$express_access_url_response = $this->stripeconnect->create_login_link($connected_account_id);
            //dd($express_access_url_response); die();

            if ($express_access_url_response['status'] == 1) {
                $express_access_url = $express_access_url_response['data']->url;
                $express_dashboard_access_url = 'https://dashboard.stripe.com/' . $connected_account_id;
            } else{
                //dd($express_access_url_response); die();
            }
        }

        return view('settings.stripe_settings', [
            'specialist' => $specialist,
            'connect_url' => $connect_url,
            'connect_stripe_account' => $connect_stripe_account,
            'express_access_url' => $express_access_url,
            'express_dashboard_access_url' => $express_dashboard_access_url,
        ]);
    }

    public function connectStripe(Request $request)
    {
        $specialist_id = $request->query('state');

        $stripe_connect_keys = [
            'publishable_key' => env('STRIPE_KEY'),
            'secret_key' => env('STRIPE_SECRET'),
            'connect_client_id' => env('STRIPE_CLIENT_ID'),
        ];

        $stripeAccount = new ReachStripeAccount;
        $connect_stripe_account = $stripeAccount->where('member_id', $specialist_id)->first();

        if (empty($connect_stripe_account)) {
            $account_code = $request->query('code');

            if ($account_code) {
                $this->stripeconnect = new StripeConnect();

                $response = $this->stripeconnect->getAccessToken($account_code);

                if (!empty($response['status'])) {
                    
                    $response = $response['data'];

                    $insert_arr = [
                        'member_id' => $specialist_id,
                        'access_token' => $response['access_token'],
                        'scope' => $response['scope'],
                        'refresh_token' => $response['refresh_token'],
                        'token_type' => $response['token_type'],
                        'stripe_user_id' => $response['stripe_user_id'],
                        'livemode' => $response['livemode'],
                        'status' => 'A',
                        'created_by' => '1',
                    ];

                    ReachStripeAccount::create($insert_arr);

                    session()->flash('success', 'Stripe linked successfully');
                } else {
                    session()->flash('error', $response['data']->getMessage());
                }
            }
        }

        return redirect()->route('settings.stripe', ['id' => $specialist_id]);
    }

    public function disconnectStripe($id) 
    {
        $stripeAccount = new ReachStripeAccount;
        $connect_stripe_account = $stripeAccount->where('member_id', $id)->first();
        
        $stripe_connect_keys = [
            'publishable_key' => env('STRIPE_KEY'),
            'secret_key' => env('STRIPE_SECRET'),
            'connect_client_id' => env('STRIPE_CLIENT_ID'),
        ];  

        $this->stripeconnect = new StripeConnect();
        $response = $this->stripeconnect->deauthorize($stripe_connect_keys['connect_client_id'], $connect_stripe_account->stripe_user_id);

        $connect_stripe_account->where(['member_id' => $id])
                ->update(['status' => 'D', 'deleted_by' => '1', 'deleted_at' => now()]);

        if ($response['status'] == 1) {
            session()->flash('success', 'Stripe unlinked successfully');
        } else {
            session()->flash('error', $response['data']->getMessage());
        }
        
        return redirect()->route('settings.stripe', ['id' => $id]);
    }

}
