<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FriendController extends Controller
{
    public function sendRequest(User $friend)
    {
        /** @var \App\Models\User|null $me */
        /** @var \App\Models\User|null $user */ 

        $user = Auth::user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->id === $friend->id) {
            return response()->json(['message' => 'لا يمكنك إضافة نفسك كصديق'], 400);
        }

        if ($user->isFriendWith($friend->id)) {
            return response()->json(['message' => 'أنتم أصدقاء بالفعل'], 400);
        }

        if ($user->hasSentRequestTo($friend->id)) {
            return response()->json(['message' => 'تم إرسال طلب صداقة مسبقًا'], 400);
        }

        $user->pendingFriendsSent()->attach($friend->id);
        Cache::forget('friends_of_user_' . $me->id);

        return response()->json(['message' => 'تم إرسال طلب الصداقة بنجاح']);
    }

    public function acceptRequest(User $user)
    {
        /** @var \App\Models\User|null $me */
        $me = Auth::user();
        if (! $me) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! $me->hasReceivedRequestFrom($user->id)) {
            return response()->json(['message' => 'لا يوجد طلب صداقة من هذا المستخدم'], 400);
        }

        $me->pendingFriendsReceived()->updateExistingPivot($user->id, ['accepted' => true]);
        Cache::forget('friends_of_user_' . $me->id);


        return response()->json(['message' => 'تم قبول طلب الصداقة، أصبحتم أصدقاء']);
    }

    public function declineRequest(User $user)
    {
        /** @var \App\Models\User|null $me */
        $me = Auth::user();
        if (! $me) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $me->pendingFriendsSent()->detach($user->id);
        $me->pendingFriendsReceived()->detach($user->id);
        Cache::forget('friends_of_user_' . $me->id);

        

        return response()->json(['message' => 'تم رفض/إلغاء الطلب']);
    }

    public function removeFriend(User $friend)
    {
        /** @var \App\Models\User|null $me */
        $me = Auth::user();
        if (! $me) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $me->friends()->detach($friend->id);
        Cache::forget('friends_of_user_' . $me->id);


        return response()->json(['message' => '']);
    }

    
    public function myFriends()
{

        /** @var \App\Models\User|null $me */

    $me = Auth::user();
    if (!$me) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $cacheKey = 'friends_of_user_' . $me->id;

    $friends = Cache::remember($cacheKey, 600, function () use ($me) {
        // استخدم use ($me) عشان الـ Closure يشوف المتغيّر
        return $me->friends()->get();
    });

    return response()->json(['friends' => $friends]);
}
    public function pendingRequests()
    {
        /** @var \App\Models\User|null $me */
        $me = Auth::user();
        if (! $me) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $requests = $me->pendingFriendsReceived()->get();
        return response()->json(['pending_requests' => $requests]);
    }
}
