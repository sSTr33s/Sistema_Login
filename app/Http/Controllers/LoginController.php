<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function register(Request $request){
        //return $request->all();
        
        //Validar datos
        $user=new User();
        $user->name=$request->name;
        $user->email=$request->email;
        $user->password=Hash::make($request->password);
        $user->save();
        
        Auth::login($user);

        return redirect(route('privada'));
    

    }
    public function login(Request $request){
     
        //Validación

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); //Prepara la session

            return redirect()->intended(route('privada'));
        }else{
            //return redirect(route('login'));
            return $request->all();
        }

    }
    public function logout(Request $request){
        
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
