<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\ReachPartner;

class UniqueDisplayOrder implements Rule
{

    protected $partnerId;

    public function __construct($partnerId=NULL)
    {
        $this->partnerId = $partnerId;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if the display order is unique
        $existingPartner = ReachPartner::where('partner_display_order', $value)->first();
        return !$existingPartner || $existingPartner->id == $this->partnerId;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The display order is already in use.';
    }
}
