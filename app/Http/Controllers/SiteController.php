<?php

namespace App\Http\Controllers;

use App\Classes\UniversalData;
use App\Models\Artist;
use App\Services\PageStyleService;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){

        $universalData = new UniversalData();

        $artists = Artist::with('label')->get();

        $styles = [];

        foreach($artists as $artist){
            $pageStyleService = new PageStyleService($artist);
            $styles[$artist->name] = $pageStyleService->getStylesByDevice();
        }

        return view('welcome', [
            'artists' => $artists,
            'styles' => $styles,
            'universalData' => $universalData,
        ]);
    }
}