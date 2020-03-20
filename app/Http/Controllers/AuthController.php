<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function dashboard()
    {
        if (Auth::check()) {
            return view('dashboard');
        }

        return redirect()->route('admin.login');


    }

    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $crendeciais = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($crendeciais)){
            return redirect()->route('admin');
        }

        return redirect()->back()->withInput()->withErrors(['Os dados nao conferem']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin');
    }
}

