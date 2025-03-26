<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\ReachTransaction;

use App\Models\StripePaymentTransaction;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $query = StripePaymentTransaction::with('member')->where('payment_type', 'bookcall');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by member name
        if ($request->has('member_name') && $request->member_name != '') {
            $query->whereHas('member', function ($q) use ($request) {
                $q->whereRaw("CONCAT(members_fname, ' ', members_lname) LIKE ?", ["%{$request->member_name}%"]);
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        $transactions->appends($request->all());

        return view('transactions.payments', ['transactions' => $transactions, 'filters' => $request->all(), 'i' => ($transactions->currentPage() - 1) * $transactions->perPage() + 1]);
    }

    public function membership(Request $request)
    {
        //$query = StripePaymentTransaction::with('member')->where('payment_type', 'membership');
        $query = StripePaymentTransaction::with(['member' => function ($query) {
            $query->withTrashed();
        }])->where('payment_type', 'membership');

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by member name
        if ($request->has('member_name') && $request->member_name != '') {
            $query->whereHas('member', function ($q) use ($request) {
                $q->whereRaw("CONCAT(members_fname, ' ', members_lname) LIKE ?", ["%{$request->member_name}%"]);
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        $transactions->appends($request->all());

        return view('transactions.membership', ['transactions' => $transactions, 'filters' => $request->all(), 'i' => ($transactions->currentPage() - 1) * $transactions->perPage() + 1]);
    }

    public function transfers(Request $request)
    {

        $query = StripePaymentTransaction::with(['member' => function ($query) {
            $query->withTrashed();
        }])->where('payment_type', 'bookcall');
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by member name
        if ($request->has('member_name') && $request->member_name != '') {
            $query->whereHas('member', function ($q) use ($request) {
                $q->whereRaw("CONCAT(members_fname, ' ', members_lname) LIKE ?", ["%{$request->member_name}%"])
                    ->orWhere('members_email', 'LIKE', "%{$request->member_name}%");
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);

        $transactions->appends($request->all());

        return view('transactions.transfers', ['transactions' => $transactions, 'filters' => $request->all(), 'i' => ($transactions->currentPage() - 1) * $transactions->perPage() + 1]);
    }

    public function transaction_history(Request $request)
    {
        $query = ReachTransaction::select(
            'id',
            'transaction_id',
            'payment_date',
            'original_amount',
            'reduced_amount',
            'actual_amount',
            'type',
            'description',
            'transaction_type',
            'rate',
            'member_id',
            'connected_member_id',
            'from_currency',
            'to_currency',
            'status'
        )
            ->with([
                'member:id,members_fname,members_lname',
                'connectedMember:id,members_fname,members_lname'
            ]);
        // Apply ordering by payment_date in descending order

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        // Filter by member name
        if ($request->has('member_name') && $request->member_name != '') {
            $query->whereHas('member', function ($q) use ($request) {
                $q->whereRaw("CONCAT(members_fname, ' ', members_lname) LIKE ?", ["%{$request->member_name}%"])
                    ->orWhere('members_email', 'LIKE', "%{$request->member_name}%")
                    ->orWhere('transaction_id', 'LIKE', "%{$request->member_name}%");
            });
        }

        $query->orderBy('payment_date', 'desc');

        // Paginate the results, 15 items per page
        $transactions = $query->paginate(15);
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->converted_original_amount = floatval(round($transaction->original_amount * $transaction->rate, 2));
            $transaction->converted_reduced_amount = floatval(round($transaction->reduced_amount * $transaction->rate, 2));
            $transaction->converted_actual_amount = floatval(round($transaction->actual_amount * $transaction->rate, 2));
            return $transaction;
        });
        //print("<PRE>");print_r( $transactions);die();
        // Return the view with the paginated transactions and additional data
        return view('transactions.transaction', [
            'transactions' => $transactions,
            'filters' => $request->all(),
            'i' => ($transactions->currentPage() - 1) * $transactions->perPage() + 1
        ]);
    }

    public function details($id)
    {
        $transaction = ReachTransaction::with(['member', 'connectedMember'])->find($id);

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.']);
        }

        $data = $transaction->getDetails();
        return response()->json(['success' => true, 'data' => $data]);
    }
}
