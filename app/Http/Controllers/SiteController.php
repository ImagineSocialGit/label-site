<?php

namespace App\Http\Controllers;

use App\Models\Favicon;
use App\Models\MetaData;
use App\Models\LegalPage;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(){
        
        $favicon = Favicon::first();
        $metaData = MetaData::first();
        $legalPages = LegalPage::all();

        return view('welcome', [
            'favicon' => $favicon,
            'metaData' => $metaData,
            'legalPages' => $legalPages,
        ]);
    }

    public function index2(){
        
        $favicon = Favicon::first();
        $metaData = MetaData::first();
        $legalPages = LegalPage::all();

        return view('alt-welcome', [
            'favicon' => $favicon,
            'metaData' => $metaData,
            'legalPages' => $legalPages,
        ]);
    }

    public function index3(){
        
        $favicon = Favicon::first();
        $metaData = MetaData::first();
        $legalPages = LegalPage::all();

        return view('alt-welcome-2', [
            'favicon' => $favicon,
            'metaData' => $metaData,
            'legalPages' => $legalPages,
        ]);
    }
}
