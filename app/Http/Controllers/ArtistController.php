<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\UniversalData;

class ArtistController extends Controller
{
    public function create(){
        $universalData = new UniversalData();

        return view('artists.create', [
            'univseralData' => $universalData,
        ]);
    }
}
