<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class CurrencyExchangeRates extends Model
{
    use HasFactory;
    protected $table = 'currency_exchange_rates';

    // Fillable fields for mass assignment
    protected $fillable = [
        'currency_code',
        'exchange_rate_to_usd',
        'exchange_rate_to_gbp',
        'exchange_rate_to_eur',
    ];

    // Disable timestamps if not needed
    public $timestamps = true;

    /**
     * Scope a query to only include rates for a specific base currency.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $baseCurrency
     * @return \Illuminate\Database\Eloquent\Builder
     */
   
}
