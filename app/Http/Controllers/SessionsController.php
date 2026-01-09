<?php

namespace App\Http\Controllers;

use App\Classes\UniversalData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SessionsController extends Controller
{
    public function create(){

        $universalData = new UniversalData(true);

        return view('sessions.create', [
            'univseralData' => $universalData
        ]);
    }

    public function store(){
        $attributes = request()->validate([
            'user' => 'required',
            'password' => 'required'
        ]);

        $attributes['env'] = config('app.env');

        if (!auth()->attempt($attributes)){
            throw ValidationException::withMessages([
                'user' => 'Your provided credentials could not be verified'
            ]);
        }

        session()->regenerate();

        return redirect('/')->with('success', 'Log In Successful!');
    }

    public function destroy()
    {
        auth()->logout();

        return redirect('/')->with('success', 'Log Out Successful!');
    }
}
