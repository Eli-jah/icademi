<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LineController extends Controller
{
    /**
     * Redirect current user request to the LINE authentication page.
     * Returned line user fields: id, name, avatar, email
     *
     * @return Response
     */
    public function login(Request $request)
    {
        return Socialite::driver('line')
            // ->setScopes([
            //     'openid',
            //     'profile',
            //     // 'email',
            // ])
            ->redirect();
    }

    /**
     * Get current user information from LINE.
     * Returned line user fields: id, name, avatar, email
     */
    public function callback(Request $request)
    {
        $user = Socialite::driver('line')
            ->user();

        // dd($user);
        // Log::info('LINE user info:' . json_encode($user));
        $line_id = $user->getId();
        $line_user = User::query()
            ->where('line_id', $line_id)
            ->firstOrCreate([
                'line_id' => $line_id,
            ], [
                'name' => $user->getName(),
                'avatar' => $user->getAvatar(),
            ]);

        Auth::guard('web')->login($line_user);

        return view('qr_code')
            ->with('line_id', $line_id);
    }
}
