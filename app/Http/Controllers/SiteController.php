<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use Illuminate\Http\Request;
use App\Classes\RefreshArtist;
use App\Classes\UniversalData;

class SiteController extends Controller
{
    public function index(){

        $universalData = new UniversalData();

        $artists = Artist::all();

        foreach($artists as $artist){
            if ($artist->token){
                
                $refreshArtist = new RefreshArtist($artist);
                
                if ($artist->music_requires_refresh){
                    $refreshArtist->fetchMusic();
                }
                if ($artist->posts_requires_refresh){

                }
                if ($artist->videos_requires_refresh){

                }
                if ($artist->design_requires_refresh){

                }
            }
        }

        return view('welcome', [
            'artists' => $artists,
            'universalData' => $universalData,
        ]);
    }
}
