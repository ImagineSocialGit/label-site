<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Label;
use App\Models\Artist;
use Illuminate\Http\Request;
use App\Classes\RefreshArtist;
use App\Classes\UniversalData;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;

class ArtistController extends Controller
{

    public function index(){
        $universalData = new UniversalData();

        $artists = Artist::with('music')->get();

        return view('artists.index', [
            'universalData' => $universalData,
            'artists' => $artists,
        ]);
    }

    public function show(Artist $artist){
        $universalData = new UniversalData();

        if ($artist->token){
                
            $refreshArtist = new RefreshArtist($artist);
            
            if ($artist->music_requires_refresh){
                $refreshArtist->fetchMusic();
            }
            if ($artist->posts_requires_refresh){
                $refreshArtist->fetchPosts();
            }
            if ($artist->videos_requires_refresh){

            }
            if ($artist->design_requires_refresh){

            }
        }

        $musicItems = $artist->music->sortByDesc('released');

        foreach($musicItems as $music){
            if ($music->isReleased()){
                $featuredRelease = $music;
                break;
            }
        }
        
        if (!isset($featuredRelease)){
            $featuredRelease = null;
        }

        return view('artists.show', [
            'universalData' => $universalData,
            'artist' => $artist,
            'featuredRelease' => $featuredRelease,
        ]);
    }

    public function showPost(Artist $artist, Post $post){

        $universalData = new UniversalData();

        //Do paragraphs
        $bodyArray = explode("\r\n", $post->body);
        $modifiedBody = '';

        foreach ($bodyArray as $paragraph){
            if (strlen($paragraph) > 1){
                $paragraph = '<p>' . $paragraph . '</p>';
                $modifiedBody .= $paragraph;
            }
        }

        $post->body = $modifiedBody;

        //Do links

        $post->title = str_replace('<link=', '<a class="underline" href=', $post->title);
        $post->title = str_replace('</link>', '</a>', $post->title);

        $post->body = str_replace('<link=', '<a class="underline" href=', $post->body);
        $post->body = str_replace('</link>', '</a>', $post->body);

        if ($post->subtitle_one){
            $post->subtitle_one = str_replace('<link=', '<a class="underline" href=', $post->subtitle_one);
            $post->subtitle_one = str_replace('</link>', '</a>', $post->subtitle_one);
        }

        if ($post->subtitle_two){
            $post->subtitle_two = str_replace('<link=', '<a class="underline" href=', $post->subtitle_two);
            $post->subtitle_two = str_replace('</link>', '</a>', $post->subtitle_two);
        }

        return view('artists.post', [
            'universalData' => $universalData,
            'artist' => $artist,
            'post' => $post,
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
        
        $artists = Artist::all()->where('env', config('app.env'));

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
            'token' => ['nullable'],
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
        
        if (isset($attributes['token'])){
            $attributes['music_requires_refresh'] = true;
            $attributes['posts_requires_refresh'] = true;
            $attributes['videos_requires_refresh'] = true;
            $attributes['design_requires_refresh'] = true;
        }
                
        $artist->update($attributes);

        return redirect('/' . $artist->slug)->with('success', 'Artist Updated');
    }
}
