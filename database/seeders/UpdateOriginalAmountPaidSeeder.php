<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateOriginalAmountPaidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        // Fetch all transactions and update the original_amount_paid field
        DB::table('stripe_payment_transaction')->get()->each(function ($transaction) {
            $amountPaid = $transaction->amount_paid;
            $discount = $transaction->discount_amount ?? 0; // Assuming 'discount' is the column for discounts
            $originalAmountPaid = $amountPaid - $discount;

            // Update the original_amount_paid field
            DB::table('stripe_payment_transaction')
                ->where('payment_id', $transaction->payment_id)
                ->update(['original_amount_paid' => $originalAmountPaid]);
        });
    }
}
