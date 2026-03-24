<?php

namespace App\Http\Controllers;

use App\Classes\UniversalData;
use App\Models\Artist;
use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    public function index(Artist $artist){
    
        $universalData = new UniversalData();

        $musicItems = $artist->music;

        return view('music.index', [
            'universalData' => $universalData,
            'artist' => $artist,
            'musicItems' => $musicItems
        ]);
    }

    public function create(Artist $artist){
    
        $universalData = new UniversalData();

        return view('music.create', [
            'universalData' => $universalData,
            'artist' => $artist,
        ]);
    }

    public function edit(Artist $artist, Music $music){
    
        $universalData = new UniversalData();

        return view('music.edit', [
            'universalData' => $universalData,
            'artist' => $artist,
            'music' => $music,
        ]);
    }

    public function store(Artist $artist){

        $validated = request()->validate([
            'title' => ['required', 'max:255'],
            'artwork' => ['required', 'file', 'mimes:png,jpg', 'max:2048'],
            'link' => ['required', 'max:255'],
            'music_link.*' => ['nullable'],
            'release_date' => ['required', 'date'],
            'presave_date' => ['date'],
            'is_holiday_release' => ['required'],
            'is_production' => ['required', 'boolean'],
        ]);

        $date = date('siH_d_m_y');

        $title = str_replace(' ', '-', $validated['title']);
        $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);

        $attributes = [
            'title' => $validated['title'],
            'link' => $validated['link'],
            'release_date' => $validated['release_date'],
            'presave_date' => $validated['presave_date'],
            'env' => config('app.env'),
        ];

        if(isset($validated['artwork'])){
            
            $type = explode('/', request()->file('artwork')->getClientMimeType())[1];
            $name = $title . '-art-' . $date . '.' . $type;

            $uploaded = Storage::putFileAs(config('app.live-site-url') . '/uploads/music', request()->file('artwork'), $name, 'public');

            if ($uploaded){
                $attributes['artwork'] = config('filesystems.disks.spaces.url') . '/uploads/music/' . $name;
            }
        }

        $artist->music()->create($attributes);

        return redirect('/admin-' . config('app.initials') . '/artists/' . $artist->slug . '/music')->with('success', 'Music Added');

    }

    public function update(Artist $artist, Music $music){

    }

    public function destroy(Artist $artist, Music $music){

        $music->delete();

        return redirect('/admin-' . config('app.initials') . '/artists/' . $artist->slug . '/music')->with('success', 'Music Deleted');
    }
}
