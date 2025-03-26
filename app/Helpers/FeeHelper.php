<?php

namespace App\Helpers;

use App\Models\MasterSetting;
use App\Models\SpecialistCallRate;

class FeeHelper
{
    /**
     * Get fee settings and call rates based on currency.
     *
     * @param string $currency The currency to filter the fees (GBP, EUR, USD).
     * @param int $specialistId The specialist ID to get the call rates.
     * @return array
     */
    public static function getFeeSettingsAndRates(string $currency, int $specialistId): array
    {
        // Fetch fee settings
        $feeSettings = MasterSetting::select(
            'specialist_booking_fee',
            'specialist_booking_fee_half_hour',
            'specialist_booking_fee_euro',
            'specialist_booking_fee_half_hour_euro',
            'specialist_booking_fee_dollar',
            'specialist_booking_fee_half_hour_dollar',
            'specialist_booking_fee_extra',
            'specialist_booking_fee_extra_euro',
            'specialist_booking_fee_extra_dollar'
        )->find(1);

        $adminSetting = [];
        if ($currency === 'EUR') {
            $adminSetting['one_EUR'] = $feeSettings['specialist_booking_fee_euro'];
            $adminSetting['half_EUR'] = $feeSettings['specialist_booking_fee_half_hour_euro'];
            $adminSetting['extra_EUR'] = $feeSettings['specialist_booking_fee_extra_euro'];
        } elseif ($currency === 'USD') {
            $adminSetting['one_USD'] = $feeSettings['specialist_booking_fee_dollar'];
            $adminSetting['half_USD'] = $feeSettings['specialist_booking_fee_half_hour_dollar'];
            $adminSetting['extra_USD'] = $feeSettings['specialist_booking_fee_extra_dollar'];
        } else {
            $adminSetting['one_GBP'] = $feeSettings['specialist_booking_fee'];
            $adminSetting['half_GBP'] = $feeSettings['specialist_booking_fee_half_hour'];
            $adminSetting['extra_GBP'] = $feeSettings['specialist_booking_fee_extra'];
        }

        // Fetch call rate
        $callRate = SpecialistCallRate::where('specialist_id', $specialistId)
            ->select('rate')
            ->orderBy('created_at', 'desc')
            ->first();

        $rateArray = $callRate ? json_decode($callRate->rate, true) : [];

        // Combine admin settings and call rate into a single response
        return [
            'adminSetting' => $adminSetting,
            'rateArray' => $rateArray
        ];
    }
}
