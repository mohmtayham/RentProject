<?php

namespace App\Http\Controllers;

use App\Models\Userwallet;
use App\Models\Transaction;
use App\Models\UserWalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if ($user->user_type === 'tenant') {
            $wallet = Userwallet::where('tenant_id', $user->id)->first();
        } elseif ($user->user_type === 'landlord') {
            $wallet = Userwallet::where('landlord_id', $user->id)->first();
        } else {
            return response()->json([
                'message' => 'Admins do not have wallets',
                'balance' => 0,
                'transactions' => []
            ], 200);
        }
        
        if (!$wallet) {
            return response()->json([
                'message' => 'Wallet not found',
                'balance' => 0,
                'transactions' => []
            ], 200);
        }
        
        // الحصول على المعاملات إذا كانت العلاقة موجودة
        $transactions = $wallet->transactions ? $wallet->transactions()->latest()->get() : [];
        
        return response()->json([
            'balance' => $wallet->balance,
            'transactions' => $transactions
        ]);
    }

    public function deposit(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        
      
        if ($user->user_type === 'tenant') {
            $wallet = Userwallet::where('tenant_id', $user->id)->first();
        } elseif ($user->user_type === 'landlord') {
            $wallet = Userwallet::where('landlord_id', $user->id)->first();
        } else {
            return response()->json(['message' => 'Admins cannot perform this action'], 400);
        }
        
        if (!$wallet) {
            // يمكنك إنشاء محفظة جديدة إذا لم توجد
            $wallet = Userwallet::create([
                'tenant_id' => $user->user_type === 'tenant' ? $user->id : null,
                'landlord_id' => $user->user_type === 'landlord' ? $user->id : null,
                'balance' => 0
            ]);
        }

        DB::transaction(function () use ($wallet, $data) {
            $wallet->increment('balance', $data['amount']);

            // إنشاء معاملة جديدة
        UserWalletTransaction::create([
                'userwallet_id' => $wallet->id, // تأكد من أن اسم العمود صحيح
                'type' => 'deposit',
                'amount' => $data['amount'],
                'description' => 'Wallet top-up',
                'status' => 'completed'
            ]);
        });

        return response()->json([
            'message' => 'Deposit successful',
            'new_balance' => $wallet->fresh()->balance
        ]);
    }

    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        
        if ($user->user_type === 'tenant') {
            $wallet = Userwallet::where('tenant_id', $user->id)->first();
        } elseif ($user->user_type === 'landlord') {
            $wallet = Userwallet::where('landlord_id', $user->id)->first();
        } else {
            return response()->json(['message' => 'Admins cannot perform this action'], 400);
        }
        
        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        if ($wallet->balance < $data['amount']) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        DB::transaction(function () use ($wallet, $data) {
            $wallet->decrement('balance', $data['amount']);

            
            UserWalletTransaction::create([
                'userwallet_id' => $wallet->id, // تأكد من أن اسم العمود صحيح
                'type' => 'withdraw',
                'amount' => $data['amount'],
                'description' => 'Wallet withdrawal',
                'status' => 'completed'
            ]);
        });

        return response()->json([
            'message' => 'Withdraw successful',
            'new_balance' => $wallet->fresh()->balance
        ]);
    }

    public function store(Request $request)
    {
      
        $user = Auth::user();
        
        $request->validate([
            'initial_balance' => 'nullable|numeric|min:0'
        ]);
      
        $existingWallet = Userwallet::where(function($query) use ($user) {
            if ($user->user_type === 'tenant') {
                $query->where('tenant_id', $user->id);
            } elseif ($user->user_type === 'landlord') {
                $query->where('landlord_id', $user->id);
            }
        })->first();
        
        if ($existingWallet) {
            return response()->json(['message' => 'Wallet already exists'], 400);
        }
        
        $wallet = Userwallet::create([
            'tenant_id' => $user->user_type === 'tenant' ? $user->id : null,
            'landlord_id' => $user->user_type === 'landlord' ? $user->id : null,
            'balance' => $request->initial_balance ?? 0
        ]);
        
        return response()->json([
            'message' => 'Wallet created successfully',
            'wallet' => $wallet
        ], 201);
    }
}