<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Artist;
use Illuminate\Http\Request;
use App\Classes\UniversalData;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{

    public function index(){
        $universalData = new UniversalData();

        $artists = Artist::all();

        return view('artists.index', [
            'universalData' => $universalData,
            'artists' => $artists,
        ]);
    }

    public function show(Artist $artist){
        $universalData = new UniversalData();

        $featuredRelease = $this->getMusic($artist->url);

        return view('artists.show', [
            'universalData' => $universalData,
            'artist' => $artist,
            'featuredRelease' => $featuredRelease,
        ]);
    }

    public function create(){
        $universalData = new UniversalData();

        $labels = Label::all();

        return view('artists.create', [
            'universalData' => $universalData,
            'labels' => $labels,
        ]);
    }

    public function edit(Artist $artist){
        $universalData = new UniversalData();

        $labels = Label::all();

        return view('artists.edit', [
            'universalData' => $universalData,
            'artist' => $artist,
            'labels' => $labels,
        ]);
    }

    public function store(){
        
        $attributes = request()->validate([
            'name' => ['required'],
            'label_id' => ['required'],
            'desktop_image' => ['required', 'image', File::types('jpg', 'jpeg', 'png')],
            'desktop_image_position' => ['nullable'],
            'mobile_image' => ['required', 'image', File::types('jpg', 'jpeg', 'png')],
            'mobile_image_position' => ['nullable'],
            'url' => ['required'],
            'about' => ['required'],
        ]);

        $date = date('siH_d_m_y');

        $name = str_replace(' ', '-', $attributes['name']);
        $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);

        if(isset($attributes['desktop_image'])){
            
            $type = explode('/', request()->file('desktop_image')->getClientMimeType())[1];
            $name = $name . '-desktop-' . $date . '.' . $type;

            $uploaded = Storage::putFileAs(config('app.name') . '/uploads/artists', request()->file('desktop_image'), $name, 'public');

            if ($uploaded){
                $attributes['desktop_image'] = '/uploads/artists/' . $name;
            }
        }

        if(isset($attributes['mobile_image'])){
            
            $type = explode('/', request()->file('mobile_image')->getClientMimeType())[1];
            $name = $name . '-mobile-' . $date . '.' . $type;

            $uploaded = Storage::putFileAs(config('app.name') . '/uploads/artists', request()->file('mobile_image'), $name, 'public');

            if ($uploaded){
                $attributes['mobile_image'] = '/uploads/artists/' . $name;
            }
        }

        $attributes['env'] = config('app.env');
        
        $artists = Artist::all()->where('env', config('app.env'));;

        $attributes['order'] = count($artists) + 1;
        
        $artist = Artist::create($attributes);
    }

    public function update(Artist $artist){
        
        $attributes = request()->validate([
            'name' => ['required'],
            'label_id' => ['required'],
            'desktop_image' => ['image', File::types('jpg', 'jpeg', 'png')],
            'desktop_image_position' => ['nullable'],
            'mobile_image' => ['image', File::types('jpg', 'jpeg', 'png')],
            'mobile_image_position' => ['nullable'],
            'url' => ['required'],
            'about' => ['required'],
        ]);

        $date = date('siH_d_m_y');

        $name = str_replace(' ', '-', $attributes['name']);
        $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);

        if(isset($attributes['desktop_image'])){
            
            $type = explode('/', request()->file('desktop_image')->getClientMimeType())[1];
            $name = $name . '-desktop-' . $date . '.' . $type;

            $uploaded = Storage::putFileAs(config('app.name') . '/uploads/artists', request()->file('desktop_image'), $name, 'public');

            if ($uploaded){
                $attributes['desktop_image'] = '/uploads/artists/' . $name;
            }
        }

        if(isset($attributes['mobile_image'])){
            
            $type = explode('/', request()->file('mobile_image')->getClientMimeType())[1];
            $name = $name . '-mobile-' . $date . '.' . $type;

            $uploaded = Storage::putFileAs(config('app.name') . '/uploads/artists', request()->file('mobile_image'), $name, 'public');

            if ($uploaded){
                $attributes['mobile_image'] = '/uploads/artists/' . $name;
            }
        }
                
        $artist->update($attributes);

        return redirect('/' . $artist->slug)->with('success', 'Artist Updated');
    }

    protected function getMusic(string $url){
        $ch = curl_init($url . '/api/music');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $return = curl_exec($ch);
        curl_close($ch);

        $decode = json_decode($return);
        $featuredMusic = (array)$decode;
        
        return $featuredMusic;
    }
}
