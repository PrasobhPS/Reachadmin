<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Firebase\JWT\JWT;

use App\Models\ReachMember;
use App\Models\ReachEmployer;
use App\Models\ReachEmployeeDetails;
use App\Models\ReachJob;
use App\Models\FcmNotification;
use App\Models\ReachMemberRefferals;
use App\Models\ReachCountry;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->only('members_email', 'members_password', 'google_token', 'facebook_token', 'apple_token', 'device_type', 'device_token');
        $token_type = "";

        if (isset($data['google_token'])) {
            // Verify Google token and get user info
            $googleUser = $this->verifyGoogleToken($data['google_token']);
            if (!$googleUser) {
                return response()->json(['error' => 'Invalid Google token.'], 401);
            }

            $member = ReachMember::where('members_email', $googleUser['email'])->first();
            if (!$member) {
                return response()->json(['error' => 'User not found.'], 201);
            }

            $token_type = "google";
            $member->update(['google_token' => $data['google_token']]);
        } elseif (isset($data['facebook_token'])) {
            // Verify Facebook token and get user info
            $facebookUser = $this->verifyFacebookToken($data['facebook_token']);
            if (!$facebookUser) {
                return response()->json(['error' => 'Invalid Facebook token.'], 401);
            }

            $member = ReachMember::where('members_email', $facebookUser['email'])->first();
            if (!$member) {
                return response()->json(['error' => 'User not found.'], 201);
            }

            $token_type = "facebook";
        } elseif (isset($data['apple_token'])) {
            // Verify Apple token and get user info
            $appleUser = $this->verifyAppleToken($data['apple_token']);

            if (!$appleUser) {
                return response()->json(['error' => 'Invalid Apple token.'], 401);
            }

            $member = ReachMember::where('members_email', $appleUser['email'])->first();
            if (!$member) {
                return response()->json(['error' => 'User not found.'], 201);
            }

            $token_type = "apple";
        } else {

            // Regular email and password authentication
            $credentials['members_email'] = $data['members_email'];
            $credentials['password'] = $data['members_password'];

            if (Auth::guard('api')->attempt($credentials)) {
                // Authentication successful
                $member = Auth::guard('api')->user();
                if ($member->is_email_verified == '0' && $member->members_status == 'I') {
                    //if ($member->is_email_verified == 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Please verify your email account.'
                    ], 400); // 400 is the status code for a bad request
                    //}
                }
            } else {
                return response()->json(['error' => 'The username or password entered is incorrect. Please try again.'], 401);
            }
        }

        if (Carbon::parse($member->members_subscription_end_date)->toDateString() < Carbon::now()->toDateString() && $member->members_type === 'M' && $member->is_specialist === 'N') {
            ReachMember::where('id', $member->id)->update(['members_type' => 'F']);
            $member->members_type = 'F';
        }
        if ($member->is_deleted === 'N' && $member->members_status === 'A') {

            // Detect if the request is from a mobile device
            $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

            // Create a new token for the current session
            $tokenName = $isMobile ? 'authToken_mobile' : 'authToken_web';

            // Revoke existing token for the same device type
            //$member->tokens()->where('name', $tokenName)->delete();

            // Create a new token for the current session
            $token = $member->createToken($tokenName)->plainTextToken;

            if (isset($data['device_token']) && $data['device_token'] != '') {

                $existingDevice = FcmNotification::where('token', $data['device_token'])->first();
                if (!$existingDevice) {

                    $device_arr = [
                        'member_id' => $member->id,
                        'token' => $data['device_token'],
                        'device_type' => $data['device_type'],
                        'is_login' => 1,
                    ];
                    FcmNotification::create($device_arr);
                } else {

                    $existingDevice->update([
                        'member_id' => $member->id,
                        'device_type' => $data['device_type'],
                        'is_login' => 1,
                    ]);
                }
            }

            if ($member->members_profile_picture != '') {
                $members_profile_picture = asset('storage/' . $member->members_profile_picture);
            } else {
                $members_profile_picture = "";
            }

            // Check if the member is an employer or an employee
            /*$isEmployer = ReachEmployer::where('employer_status', 'A')
                ->where('member_id', $member->id)
                ->exists() ? 'Y' : 'N';*/
            $isEmployer = ReachJob::where('member_id', $member->id)
                ->exists() ? 'Y' : 'N';

            $employee = ReachEmployeeDetails::where('member_id', $member->id)
                ->first();
            $employeeStatus = 'I';
            if ($employee) {
                $employeeId = $employee->employee_id;
                $isEmployee = 'Y';

                if ($employee->employee_status == "A") {
                    $employeeStatus = 'A';
                }
            } else {
                $employeeId = "";
                $isEmployee = 'N';
            }

            $currentServerTimezone = Carbon::now('Europe/London');
            $time = $currentServerTimezone->format('Y-m-d H:i:s');
            $country_iso = 'GB';
            if ($member->members_country) {
                $country = ReachCountry::where('country_name', $member->members_country)->first();
                $country_iso = $country->country_iso;
            }


            return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => $member->members_type, 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'members_profile_picture' => $members_profile_picture, 'IsEmployer' => $isEmployer, 'IsEmployee' => $isEmployee, 'employeeId' => $employeeId, 'employee_status' => $employeeStatus, 'is_specialist' => $member->is_specialist, 'subscription_status' => $member->subscription_status, 'token_type' => $token_type, 'date_time' => $time, 'stripe_account_id' => $member->stripe_account_id, 'currency' => $member->currency, 'country' => $country_iso]], 200);
        } elseif ($member->members_status === 'I' && $member->is_deleted === 'N') {

            $member->update(['members_status' => 'A']);
            $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);

            // Create a new token for the current session
            $tokenName = $isMobile ? 'authToken_mobile' : 'authToken_web';

            // Revoke existing token for the same device type
            //$member->tokens()->where('name', $tokenName)->delete();

            // Create a new token for the current session
            $token = $member->createToken($tokenName)->plainTextToken;

            if ($member->members_profile_picture != '') {
                $members_profile_picture = asset('storage/' . $member->members_profile_picture);
            } else {
                $members_profile_picture = "";
            }

            // Check if the member is an employer or an employee
            /*$isEmployer = ReachEmployer::where('employer_status', 'A')
                ->where('member_id', $member->id)
                ->exists() ? 'Y' : 'N';*/
            $isEmployer = ReachJob::where('member_id', $member->id)
                ->exists() ? 'Y' : 'N';

            $employee = ReachEmployeeDetails::where('member_id', $member->id)
                ->first();
            $employeeStatus = 'I';
            if ($employee) {
                $employeeId = $employee->employee_id;
                $isEmployee = 'Y';

                if ($employee->employee_status == "A") {
                    $employeeStatus = 'A';
                }
            } else {
                $employeeId = "";
                $isEmployee = 'N';
            }

            $currentServerTimezone = Carbon::now('Europe/London');
            $time = $currentServerTimezone->format('Y-m-d H:i:s');
            $country_iso = 'GB';
            if ($member->members_country) {
                $country = ReachCountry::where('country_name', $member->members_country)->first();
                $country_iso = $country->country_iso;
            }
            return response()->json(['success' => true, 'message' => 'OK', 'data' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => $member->members_type, 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'members_profile_picture' => $members_profile_picture, 'IsEmployer' => $isEmployer, 'IsEmployee' => $isEmployee, 'employeeId' => $employeeId, 'employee_status' => $employeeStatus, 'is_specialist' => $member->is_specialist, 'subscription_status' => $member->subscription_status, 'token_type' => $token_type, 'date_time' => $time, 'stripe_account_id' => $member->stripe_account_id, 'currency' => $member->currency, 'country' => $country_iso]], 200);
        } elseif ($member->members_status === 'I' && $member->is_deleted === 'Y') {

            return response()->json([
                'error' => 'Your account has been permanently deleted. Please contact support if this is a mistake.'
            ], 401);
        } else {

            return response()->json(['error' => 'Your account is deactivated now, Please contact administrator.'], 401);
        }

        // Authentication failed
        return response()->json(['error' => 'The Username or Password is Incorrect. Try again.'], 201);
    }

    public function iosLogin(Request $request)
    {
        $data = $request->only('ios_token');
        $data['ios_token'] = base64_decode($data['ios_token']);
        $member = ReachMember::where('ios_payment_token', $data['ios_token'])->first();
        if (!$member) {
            return response()->json([
                'success' => false,
                'error' => 'Member not found. Please check your token or try again.'
            ], 404);
        }
        if ($member->is_deleted === 'N' && $member->members_status === 'A') {

            // Detect if the request is from a mobile device
            $isMobile = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
            $tokenName = $isMobile ? 'authToken_mobile' : 'authToken_web';
            $token = $member->createToken($tokenName)->plainTextToken;
            if ($member->members_profile_picture != '') {
                $members_profile_picture = asset('storage/' . $member->members_profile_picture);
            } else {
                $members_profile_picture = "";
            }

            $isEmployer = ReachJob::where('member_id', $member->id)
                ->exists() ? 'Y' : 'N';

            $employee = ReachEmployeeDetails::where('member_id', $member->id)
                ->first();
            $employeeStatus = 'I';
            if ($employee) {
                $employeeId = $employee->employee_id;
                $isEmployee = 'Y';

                if ($employee->employee_status == "A") {
                    $employeeStatus = 'A';
                }
            } else {
                $employeeId = "";
                $isEmployee = 'N';
            }

            $currentServerTimezone = Carbon::now('Europe/London');
            $time = $currentServerTimezone->format('Y-m-d H:i:s');
            ReachMember::where('id', $member->id)
                ->update(['ios_payment_token' => null]);

            $memberDetails = $member->only(['id', 'members_name_title', 'members_fname', 'members_lname', 'members_email', 'members_phone', 'members_phone_code', 'members_dob', 'members_address', 'members_country', 'members_region', 'members_postcode', 'members_town', 'members_street', 'members_profile_picture', 'members_interest', 'members_employment', 'members_employment_history', 'members_biography', 'members_about_me', 'members_type', 'members_subscription_plan', 'members_subscription_start_date', 'members_subscription_end_date', 'subscription_status', 'is_specialist', 'referral_code']);
            if ($member->members_type != 'M') {
                $referral = ReachMemberRefferals::where('member_id', $memberDetails['id'])
                    ->select('refferal_code') // Adjust this based on your schema
                    ->first();

                $memberDetails['referral_code'] = $referral ? $referral->refferal_code : null; // This will be null if no referral exists
            }

            return response()->json(['success' => true, 'message' => 'OK', 'userData' => ['Token' => $token, 'Member_id' => $member->id, 'Member_type' => $member->members_type, 'Member_fullname' => $member->members_fname . ' ' . $member->members_lname, 'members_profile_picture' => $members_profile_picture, 'IsEmployer' => $isEmployer, 'IsEmployee' => $isEmployee, 'employeeId' => $employeeId, 'employee_status' => $employeeStatus, 'is_specialist' => $member->is_specialist, 'subscription_status' => $member->subscription_status, 'date_time' => $time, 'stripe_account_id' => $member->stripe_account_id, 'currency' => $member->currency], 'data' => $memberDetails], 200);
        } else {

            return response()->json(['error' => 'Your account is deactivated now, Please contact administrator.'], 401);
        }

        // Authentication failed
        return response()->json(['error' => 'The Username or Password is Incorrect. Try again.'], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $requestData = $request->all();

        if ($user) {
            $accessToken = $user->currentAccessToken();
            if ($accessToken) {

                // Revoke Google token if it exists
                // $googleToken = ReachMember::where('id', $user->id)->whereNotNull('google_token')->first();
                // if ($googleToken) {
                //     $this->revokeGoogleToken($googleToken->google_token);
                // }

                $accessToken->delete();

                if (isset($requestData['device_token']) && $requestData['device_token'] != '') {
                    $existingDevice = FcmNotification::where('token', $requestData['device_token'])->first();
                    if ($existingDevice) {
                        $existingDevice->update([
                            'is_login' => 0,
                        ]);
                    }
                }
            }
            return response()->json(['message' => 'Logged out successfully'], 200);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

    private function verifyGoogleToken($token)
    {
        $response = Http::get('https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $token);
        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    private function revokeGoogleToken($token)
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/revoke', [
            'token' => $token,
        ]);

        if ($response->failed()) {
            // Handle the error response
            return false;
        }

        return true;
    }

    private function verifyFacebookToken($token)
    {
        $response = Http::get('https://graph.facebook.com/me?fields=id,name,email&access_token=' . $token);
        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    private function verifyAppleToken($token)
    {
        // Apple token verification logic
        $clientID = env('APPLE_CLIENT_ID');
        $clientSecret = $this->generateAppleClientSecret();

        // Make a POST request to Apple's token endpoint
        $response = Http::asForm()->post('https://appleid.apple.com/auth/token', [
            'client_id' => $clientID,
            'client_secret' => $clientSecret,
            'code' => $token,
            'grant_type' => 'authorization_code',
        ]);

        // Check if the response is successful
        if ($response->successful()) {
            $data = $response->json();

            // Ensure the `id_token` exists in the response
            if (isset($data['id_token'])) {
                $id_token = $data['id_token'];

                // Decode the JWT (extract the payload part)
                $tokenParts = explode('.', $id_token);
                if (count($tokenParts) === 3) {
                    $payload = json_decode(base64_decode($tokenParts[1]), true);

                    if (isset($payload['email']) && isset($payload['sub'])) {
                        return $payload;
                    }
                }
            }
        }

        return null;
    }

    private function verifyAppleToken_Curl($token)
    {
        $clientID = env('APPLE_CLIENT_ID');
        $clientSecret = $this->generateAppleClientSecret();
        $redirectUri = 'https://reach-492da.firebaseapp.com/__/auth/handler';

        // Setup the cURL request
        $url = 'https://appleid.apple.com/auth/token';
        $data = [
            'client_id' => $clientID,
            'client_secret' => $clientSecret,
            'code' => $token,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ];

        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        // Get the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // If there's an error, print it
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close the cURL session
        curl_close($ch);

        // Decode the response to handle it
        $responseData = json_decode($response, true);
        print_r($responseData);
        exit();
    }

    private function generateAppleClientSecret()
    {
        // Generate the Apple client secret
        $keyFile = storage_path('app/public/apple/AuthKey_NST4A9UY4A.p8');
        $keyId = env('APPLE_KEY_ID');
        $teamId = env('APPLE_TEAM_ID');
        $clientId = env('APPLE_CLIENT_ID');

        $header = [
            'alg' => 'ES256',
            'kid' => $keyId,
        ];
        $body = [
            'iss' => $teamId,
            'iat' => time(),
            'exp' => time() + 3600,
            'aud' => 'https://appleid.apple.com',
            'sub' => $clientId,
        ];

        // Load the private key
        $privateKey = file_get_contents($keyFile);

        // Generate the JWT
        $jwt = JWT::encode($body, $privateKey, 'ES256', $keyId, $header);

        return $jwt;
    }
}
