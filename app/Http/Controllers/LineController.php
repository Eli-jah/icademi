<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class LineController extends Controller
{
    /**
     * Redirect current user request to the LINE authentication page.
     *
     * Returned line user fields: id, name, avatar, email
     *
     * @return Response
     */
    public function login(Request $request)
    {
        return Socialite::driver('line')->redirect();
    }

    /**
     * Get current user information from LINE.
     *
     * Returned line user fields: id, name, avatar, email
     *
     * @return Response
     */
    public function callback(Request $request)
    {
        $user = Socialite::driver('line')->user();
        $line_id = $user->getId();
        return view('qr_code')->with('line_id', $line_id);
    }

    /**
     * Get current user QR Code from LINE.
     *
     * @return Response
     */
    public function qrCode(Request $request)
    {
        $user = Socialite::driver('line')->user();

        // dd($user);
        // $user->token;
        $line_id = $user->getId();
        return view('qr_code')->with('line_id', $line_id);
    }
}
