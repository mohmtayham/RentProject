<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

   public function sendOtp(Request $request)
{
    $request->validate(['phone' => 'required|string']);
    $otp = $this->otpService->createOtp($request->phone);
    $sent = $this->otpService->attemptSendOtp($request->phone, $otp);

    if (app()->environment('local')) {
        return response()->json([
            'message' => 'OTP for development',
            'phone' => $request->phone,
            'otp' => $otp 
        ]);
    }

    if (!$sent) {
        return response()->json(['message' => 'فشل الإرسال'], 500);
    }

    return response()->json(['message' => 'تم الإرسال']);
}

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string'
        ]);

        $result = $this->otpService->verifyOtp($request->phone, $request->code);

        if (is_string($result)) {
            return response()->json([
                'message' => $result
            ], 400);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => $result['user']
        ]);
    }
}