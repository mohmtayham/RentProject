<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

{
    /**
     * Display a listing of the resource.
     */
class WalletController extends Controller
{
    public function show()
    {
        $wallet = auth()->user()->wallet;

        return response()->json([
            'balance' => $wallet->balance,
            'transactions' => $wallet->transactions()->latest()->get(),
        ]);
    }

    public function deposit(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = auth()->user()->wallet;

        DB::transaction(function () use ($wallet, $data) {
            $wallet->increment('balance', $data['amount']);

            $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $data['amount'],
                'description' => 'Wallet top-up',
            ]);
        });

        return response()->json(['message' => 'Deposit successful']);
    }

    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = auth()->user()->wallet;

        if ($wallet->balance < $data['amount']) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        DB::transaction(function () use ($wallet, $data) {
            $wallet->decrement('balance', $data['amount']);

            $wallet->transactions()->create([
                'type' => 'withdraw',
                'amount' => $data['amount'],
                'description' => 'Wallet withdrawal',
            ]);
        });

        return response()->json(['message' => 'Withdraw successful']);
    }
}
}
