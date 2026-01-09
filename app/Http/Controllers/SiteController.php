<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Classes\UniversalData;

class SiteController extends Controller
{
    public function index(){

        $universalData = new UniversalData();

        $artists = Artist::all();

        return view('welcome', [
            'artists' => $artists,
            'universalData' => $universalData,
        ]);
    }
}
