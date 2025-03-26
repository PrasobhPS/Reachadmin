<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\MasterSetting;
use App\Models\ReachMember;
use App\Models\CurrencyExchangeRates;
use App\Models\SpecialistCallRate;
use App\Models\Specialist_call_schedule;
class CurrencyService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('EXCHANGERATE_KEY');
        $this->apiEndpoint = 'https://api.currencylayer.com/'; // Endpoint for the API
    }

    public function convertCurrency($fromCurrency, $toCurrency, $amount)
    {
        $endpoint = 'convert';
        $url = "{$this->apiEndpoint}{$endpoint}?access_key={$this->apiKey}&from={$fromCurrency}&to={$toCurrency}&amount={$amount}";

        try {
            // Initialize CURL:
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute the API call and store the response
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response
            $conversionResult = json_decode($json, true);

            // Check for errors in the API response
            if (isset($conversionResult['error'])) {
                throw new Exception($conversionResult['error']['info'] ?? 'Unknown error');
            }

            return $conversionResult['result'];

        } catch (Exception $e) {
            return null;
        }
    }

    // API request for USD, GBP, EUR rates
    public function getExchangeRates($currency)
    {
        $endpoint = 'live';
        $url = "{$this->apiEndpoint}{$endpoint}?access_key={$this->apiKey}&source={$currency}";

        try {
            // Initialize CURL:
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute the API call and store the response
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response
            $exchangeRates = json_decode($json, true);

            // Check for errors in the API response
            if (isset($exchangeRates['error'])) {
                throw new Exception($exchangeRates['error']['info'] ?? 'Unknown error');
            }

            // You can return the required exchange rates (USD to GBP, EUR)
            if ($currency == "USD") {
                return [
                    'USD' => 1,
                    'GBP' => $exchangeRates['quotes']['USDGBP'],
                    'EUR' => $exchangeRates['quotes']['USDEUR'],
                ];
            } elseif ($currency == "GBP") {
                return [
                    'USD' => $exchangeRates['quotes']['GBPUSD'],
                    'GBP' => 1,
                    'EUR' => $exchangeRates['quotes']['GBPEUR'],
                ];
            } elseif ($currency == "EUR") {
                return [
                    'USD' => $exchangeRates['quotes']['EURUSD'],
                    'GBP' => $exchangeRates['quotes']['EURGBP'],
                    'EUR' => 1,
                ];
            } else {
                return null;
            }

        } catch (Exception $e) {
            return null;
        }
    }

    public function getMembershipFee($requestData)
    {
        // Retrieve the membership fee
        $feeSettings = MasterSetting::select(
            'full_membership_fee',
            'monthly_membership_fee',
            'full_membership_fee_euro',
            'full_membership_fee_dollar',
            'monthly_membership_fee_euro',
            'monthly_membership_fee_dollar'
        )->find(1);


        if ($requestData['subscription_plan'] == "Monthly") {
            if ($requestData['currency'] == 'EUR') {
                $membership_fee = $feeSettings['monthly_membership_fee_euro'];
            } else if ($requestData['currency'] == 'USD') {
                $membership_fee = $feeSettings['monthly_membership_fee_dollar'];
            } else {
                $membership_fee = $feeSettings['monthly_membership_fee'];
            }
        } else {
            if ($requestData['currency'] == 'EUR') {
                $membership_fee = $feeSettings['full_membership_fee_euro'];
            } else if ($requestData['currency'] == 'USD') {
                $membership_fee = $feeSettings['full_membership_fee_dollar'];
            } else {
                $membership_fee = $feeSettings['full_membership_fee'];
            }

        }
        $discountAmount = 0;
        $referralDiscount = 0;
        $coupon_id = null;
        if (isset($requestData['referral_code']) && $requestData['referral_code']) {
            $referral_dts = ReachMember::where('referral_code', $requestData['referral_code'])->first();
            // Handle referral discount
            if (!empty($referral_dts)) {

                if ($requestData['subscription_plan'] == "Monthly") {
                    if ($referral_dts['currency'] == 'EUR') {
                        $parent_membership_fee = $feeSettings['monthly_membership_fee_euro'];
                    } else if ($referral_dts['currency'] == 'USD') {
                        $parent_membership_fee = $feeSettings['monthly_membership_fee_dollar'];
                    } else {
                        $parent_membership_fee = $feeSettings['monthly_membership_fee'];
                    }
                } else {
                    if ($referral_dts['currency'] == 'EUR') {
                        $parent_membership_fee = $feeSettings['full_membership_fee_euro'];
                    } else if ($referral_dts['currency'] == 'USD') {
                        $parent_membership_fee = $feeSettings['full_membership_fee_dollar'];
                    } else {
                        $parent_membership_fee = $feeSettings['full_membership_fee'];
                    }

                }

                $referral_rate = $referral_dts->referral_rate / 100;
                // $discountAmount = $membership_fee * 0.10;
                $discountAmount = $membership_fee * $referral_rate;
                // if ($requestData['subscription_plan'] == "Monthly") {
                //     $discountAmount =  $referralDiscount ;
                // } else {
                //     $discountAmount = $membership_fee * $referral_rate;
                // }
                // Create a coupon in Stripe for 10% off


            }
        }
        $actual_fee = $membership_fee - $discountAmount;
        $parent_currency = isset($referral_dts['currency']) ? $referral_dts['currency'] : 0;

        // Return the necessary values (membership fee, discount fee, actual fee)
        return [
            'membership_fee' => $membership_fee,
            'discount_amount' => $discountAmount,
            'actual_amount' => $actual_fee,
            'parent_currency' => $parent_currency,
        ];
    }

    public function getCurrencyRate($fromCurrency, $toCurrency)
    {
        // Retrieve the exchange rate from the currency_rates table
        $currencyRate = CurrencyExchangeRates::where('currency_code', $fromCurrency)
            ->select('currency_code', 'exchange_rate_to_usd', 'exchange_rate_to_gbp', 'exchange_rate_to_eur')
            ->first();

        if (!$currencyRate) {
            return null;  // If no exchange rate found for the fromCurrency
        }

        // Case when converting from USD to other currencies
        if ($fromCurrency === 'USD') {
            if ($toCurrency === 'EUR') {
                return $currencyRate->exchange_rate_to_eur;
            } elseif ($toCurrency === 'GBP') {
                return $currencyRate->exchange_rate_to_gbp;
            } elseif ($toCurrency === 'USD') {
                return 1;
            }
        }

        // Case when converting from GBP to other currencies
        if ($fromCurrency === 'GBP') {
            if ($toCurrency === 'USD') {
                return $currencyRate->exchange_rate_to_usd;  // Convert GBP to USD
            } elseif ($toCurrency === 'EUR') {
                return $currencyRate->exchange_rate_to_eur;  // GBP -> EUR
            } elseif ($toCurrency === 'GBP') {
                return 1;  // GBP -> EUR
            }
        }

        // Case when converting from EUR to other currencies
        if ($fromCurrency === 'EUR') {
            if ($toCurrency === 'USD') {
                return $currencyRate->exchange_rate_to_usd;  // Convert EUR to USD
            } elseif ($toCurrency === 'GBP') {
                return $currencyRate->exchange_rate_to_gbp;  // EUR -> GBP
            } elseif ($toCurrency === 'EUR') {
                return 1;  // GBP -> EUR
            }
        }

        // If no exchange rate found for the given pair
        return null;
    }
    public function getspecialistFee($requestData, $member_id)
    {
       
        $feeSettings = MasterSetting::select(
            'specialist_booking_fee',
            'specialist_booking_fee_half_hour',
            'specialist_booking_fee_extra',
            'specialist_booking_fee_euro',
            'specialist_booking_fee_half_hour_euro',
            'specialist_booking_fee_extra_euro',
            'specialist_booking_fee_dollar',
            'specialist_booking_fee_half_hour_dollar',
            'specialist_booking_fee_extra_dollar'
        )->find(1);

        if ($requestData['currency'] === 'EUR') {
            $adminSetting['one_EUR'] = $feeSettings['specialist_booking_fee_euro'];
            $adminSetting['half_EUR'] = $feeSettings['specialist_booking_fee_half_hour_euro'];
            $adminSetting['extra_EUR'] = $feeSettings['specialist_booking_fee_extra_euro'];
        } else if ($requestData['currency'] === 'USD') {
            $adminSetting['one_USD'] = $feeSettings['specialist_booking_fee_dollar'];
            $adminSetting['half_USD'] = $feeSettings['specialist_booking_fee_half_hour_dollar'];
            $adminSetting['extra_USD'] = $feeSettings['specialist_booking_fee_extra_dollar'];
        } else {
            $adminSetting['one_GBP'] = $feeSettings['specialist_booking_fee'];
            $adminSetting['half_GBP'] = $feeSettings['specialist_booking_fee_half_hour'];
            $adminSetting['extra_GBP'] = $feeSettings['specialist_booking_fee_extra'];
        }

        $callRate = SpecialistCallRate::where('specialist_id', $requestData['specialist_id'])
            ->select('rate')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($callRate) {
            $rateArray = json_decode($callRate->rate, true);
        } else {
            $rateArray = []; // or set a default value
        }

        if ($requestData['timeSlot'] === '1hr' || $requestData['timeSlot'] === '1 hour') {
            $type = 'one_';
        } else {
            $type = 'half_';
        }

        if (!empty($requestData['meeting_id'])) {

            $parentSchedule = Specialist_call_schedule::where('member_id', $member_id)
                ->where('meeting_id', $requestData['meeting_id'])
                ->first();

            if ($parentSchedule) {
                $type = 'extra_';
            }
        }


        $rateIndex = $type . '' . $requestData['currency'];
        $amount = (($rateArray && $rateArray[$rateIndex]) ? $rateArray[$rateIndex] : $adminSetting[$rateIndex]) * 100;
        $feeSettings = MasterSetting::select('reach_fee')->find(1);

        $stripe_change = 3;
        $service_fee = (($feeSettings['reach_fee'] + $stripe_change) / 100) * $amount / 100;
        $transfer_amounts = ($amount / 100) - $service_fee;
        return [
            'member_fee' => ($amount / 100),
            'discount_amount' => $service_fee,
            'actual_amount' => ($amount / 100),
            'parent_currency' => $requestData['currency'],
        ];
    }



}
