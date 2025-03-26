<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use Laravel\Sanctum\HasApiTokens;

class ReachMember extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes;

    protected $table = 'reach_members';

    protected $fillable = [
        'members_fname',
        'members_lname',
        'members_email',
        'members_password',
        'members_phone',
        'members_dob',
        'members_address',
        'members_status',
        'members_payment_status',
        'members_country',
        'members_region',
        'members_employment',
        'members_employment_history',
        'members_biography',
        'members_interest',
        'members_about_me',
        'members_profile_picture',
        'members_type',
        'members_phone_code',
        'members_name_title',
        'members_postcode',
        'is_deleted',
        'deleted_date',
        'deleted_by',
        'password_reset_token',
        'members_town',
        'members_street',
        'is_specialist',
        'members_subscription_plan',
        'members_subscription_start_date',
        'members_subscription_end_date',
        'google_token',
        'referral_code',
        'referral_type_id',
        'referral_rate',
        'password_reset_time',
        'subscription_status',
        'stripe_account_id',
        'currency',
        'stripe_account_url',
        'verification_id',
        'is_doc_verified',
        'doc_verified_at',
        'ios_payment_token',
        'email_verify_token',
        'is_email_verified'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'members_password',
        // Add other hidden fields as needed
    ];

    // Define a local scope
    public function scopeActive($query)
    {
        return $query->where('members_status', 'A');
    }

    public function getAuthPassword()
    {
        return $this->members_password;
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->members_password,
        );
    }

    public static function generateReferralCode()
    {
        do {
            $code = Str::random(8);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function specialistSchedules()
    {
        return $this->hasMany(Specialist_call_schedule::class, 'specialist_id', 'id');
    }

    public function memberSchedules()
    {
        return $this->hasMany(Specialist_call_schedule::class, 'member_id', 'id');
    }

    public function specialistTransaction()
    {
        return $this->hasMany(StripePaymentTransaction::class, 'payment_to', 'id');
    }

    public function memberTransaction()
    {
        return $this->hasMany(StripePaymentTransaction::class, 'member_id', 'id');
    }

    public function memberReffered()
    {
        return $this->hasMany(ReachMemberRefferals::class, 'refferal_member_id', 'id');
    }

    public function memberRefferal()
    {
        return $this->hasOne(ReachMemberRefferals::class, 'member_id', 'id');
    }

    public function EmployeeDetails()
    {
        return $this->hasOne(ReachEmployeeDetails::class, 'member_id', 'id');
    }

    public function ratings()
    {
        return $this->hasMany(SpecialistRating::class, 'member_id', 'id');
    }
}
