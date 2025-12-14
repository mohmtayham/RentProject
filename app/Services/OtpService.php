<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;


class OtpService
{
    private string $apiKey;
    private string $apiUrl;
    private string $appleReviewPhone;
    private string $appleStaticOtp;

    public function __construct()
    {
        $this->apiKey = config('otp.sms_api_key');
        $this->apiUrl = config('otp.sms_api_url');
        $this->appleReviewPhone = config('otp.apple_review_phone');
        $this->appleStaticOtp = config('otp.apple_static_otp');
    }
    
    // Rest of your methods...


    /**
     * ⬅️ إرسال OTP (قديم)
     * ⚠️ غير مستخدم في المشروع الحالي
     */
    public function sendOtp(string $phone): void
    {
        // حالة Apple Review: استخدام OTP ثابت
        if ($phone === $this->appleReviewPhone) {
            $otp = $this->appleStaticOtp;

            Otp::create([
                'phone' => $phone,
                'otp' => $otp,
                'used' => false,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ]);

            Log::channel('single')->info('[APPLE REVIEW] sendOtp bypassed. Static OTP stored.', [
                'phone' => $phone,
                'otp' => $otp,
            ]);
            return;
        }

        $otp = rand(100000, 999999);

        Otp::create([
            'phone' => $phone,
            'otp' => $otp,
            'used' => false,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ]);

        $message = "كود التحقق الخاص بك هو: $otp. يرجى عدم مشاركته مع أي شخص.";

        // ⬅️ لوغ قبل الإرسال
        Log::channel('single')->info('[SMS][OUTBOUND][sendOtp] Preparing to send OTP SMS.', [
            'to' => $phone,
            'message' => $message,
        ]);

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->apiUrl, [
            'to' => $phone,
            'message' => $message
        ]);

        Log::channel('single')->info('[SMS][OUTBOUND][sendOtp] HTTP Response', [
            'to' => $phone,
            'status_code' => $response->status(),
            'body' => $response->body(),
        ]);
    }

    /**
     * ===== توليد وإرسال OTP =====
     */

    public function createOtp(string $phone): string
    {
        // حالة Apple Review: استخدام OTP ثابت
        if ($phone === $this->appleReviewPhone) {
            $otp = $this->appleStaticOtp;

            Otp::create([
                'phone' => $phone,
                'otp' => $otp,
                'used' => false,
                'created_at' => now(),
                'expires_at' => now()->addMinutes(10),
            ]);

            Log::channel('single')->info('[APPLE REVIEW] Static OTP created.', [
                'phone' => $phone,
                'otp' => $otp,
            ]);

            return $otp;
        }

        $otp = (string) rand(100000, 999999);

        Otp::create([
            'phone' => $phone,
            'otp' => $otp,
            'used' => false,
            'created_at' => now(),
            'expires_at' => now()->addMinutes(10),
        ]);

        // ⬅️ لوغ إنشاء OTP
        Log::channel('single')->info('[OTP] OTP created (not yet sent).', [
            'phone' => $phone,
            'otp' => $otp,
        ]);

        return $otp;
    }

    /**
     * ⬅️ محاولة إرسال OTP عبر cURL
     */
    public function attemptSendOtp(string $phone, string $otp): bool
    {
        try {
            // حالة Apple Review: تجاوز الإرسال الحقيقي
            if ($phone === $this->appleReviewPhone) {
                Log::channel('single')->info('[APPLE REVIEW] attemptSendOtp bypassed (no real SMS).', [
                    'phone' => $phone,
                    'otp' => $otp,
                ]);
                return true;
            }

            $message = "كود التحقق الخاص بك هو: $otp. يرجى عدم مشاركته مع أي شخص.";

            // ⬅️ لوغ قبل الإرسال
            Log::channel('single')->info('[SMS][OUTBOUND][attemptSendOtp] Preparing to send OTP SMS.', [
                'to' => $phone,
                'message' => $message,
            ]);

            $postData = json_encode([
                'to' => $phone,
                'message' => $message
            ]);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->apiKey,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSLVERSION => CURL_SSLVERSION_DEFAULT,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            // ⬅️ لوغ بعد الإرسال
            Log::channel('single')->info('[SMS][OUTBOUND][attemptSendOtp] HTTP Response', [
                'to' => $phone,
                'status_code' => $httpCode,
                'body' => $response,
            ]);

            if ($error) {
                Log::channel('single')->error('[SMS][OUTBOUND][attemptSendOtp] cURL Error', [
                    'to' => $phone,
                    'error' => $error,
                ]);
                return false;
            }

            $ok = $httpCode >= 200 && $httpCode < 300;

            Log::channel('single')->info('[SMS][OUTBOUND][attemptSendOtp] Result', [
                'to' => $phone,
                'status' => $ok ? 'sent' : 'failed',
            ]);

            return $ok;

        } catch (\Exception $e) {
            Log::channel('single')->error('SMS sending error: ' . $e->getMessage(), [
                'to' => $phone,
            ]);
            return false;
        }
    }

    /**
     * ✅ التحقق من OTP وإرجاع التوكن
     */
   /**
 * ✅ التحقق من OTP وإرجاع التوكن
 */
public function verifyOtp(string $phone, string $code): array|string
{
    // حالة Apple Review: قبول الكود الثابت
    if ($phone === $this->appleReviewPhone && $code === $this->appleStaticOtp) {
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return 'User not found for Apple review phone.';
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::channel('single')->info('[APPLE REVIEW] OTP verified via static code.', [
            'phone' => $phone,
        ]);

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    $otp = Otp::where('phone', $phone)
        ->where('otp', $code)
        ->where('used', false)
        ->where('expires_at', '>', now())
        ->latest()
        ->first();

    if (!$otp) {
        Log::channel('single')->warning('[OTP] Invalid or expired OTP attempt.', [
            'phone' => $phone,
            'code' => $code,
        ]);
        return 'OTP is invalid or expired';
    }

    $otp->update(['used' => true]);

    $user = User::where('phone', $phone)->firstOrFail();

    // توليد التوكن (Sanctum)
    $token = $user->createToken('auth_token')->plainTextToken;

    Log::channel('single')->info('[OTP] OTP verified successfully.', [
        'phone' => $phone,
        'user_id' => $user->id,
    ]);

    return [
        'token' => $token,
        'user' => $user,
    ];
}
    
}