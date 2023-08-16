<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class AuthController extends Controller
{

    public function viewRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create user
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
        ]);

        $user->save();

        return redirect()->route('login');
    }


    public function viewLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            return redirect()->back()->withErrors(['error' => 'Unauthorized']);
        }

        $user = $request->user();
        if (!$user || $user->role !== 'Admin') {
            return redirect()->back()->withErrors(['error' => 'You are not allowed']);
        }

        return redirect()->route('dashboard', ['token' => $token]);
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         // 'token_type' => 'bearer',
    //         // 'expires_in' => auth()->factory()->getTTL() * 60,
    //     ]);
    // }


    public function signInwithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackToGoogle()
    {
        $user = Socialite::driver('google')->user();
        // Find or create user based on SSO data
        // ...

        $finduser = User::where('google_id', $user->id)->first();

        if ($finduser) {

            Auth::login($finduser);

            return redirect('/dashboard');
        } else {
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
                'password' => encrypt(rand(5, 15)),
            ]);

            // Auth::login($newUser);
            Auth::login($newUser);

            return redirect('/dashboard');
        }
    }
}
