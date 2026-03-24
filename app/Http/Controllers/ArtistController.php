<?php

namespace App\Http\Controllers;

use App\Classes\RefreshArtist;
use App\Classes\UniversalData;
use App\Models\Artist;
use App\Models\Label;
use App\Models\Post;
use App\Services\PageStyleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Nette\Utils\Image;

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

        // Refresh artist if needed
        if ($artist->token && $artist->requiresRefresh()) {
            (new RefreshArtist($artist))->refresh($artist->refreshFlags());
        }

        $pageStyleService = new PageStyleService($artist);

        $styles = $pageStyleService->getStylesByDevice();

        $musicItems = $artist->music->sortByDesc('release_date');

        foreach($musicItems as $music){
            if ($music->isPresave() || $music->isReleased()){
                $featuredRelease = $music;
                break;
            }
        }
        
        if (!isset($featuredRelease)){
            $featuredRelease = null;
        }

        if ($universalData->showLivePosts){
            $artist->load(['posts' => function ($query) {
                $query->where(function ($q) {
                    $q->where('env', 'production');
                });
            }]);
        } else {
            $artist->load(['posts' => function ($query) {
                $query->where(function ($q) {
                    $q->where('env', config('app.env'))
                    ->orWhereNotNull('external_site_id');
                });
            }]);
        }

        return view('artists.show', [
            'universalData' => $universalData,
            'artist' => $artist,
            'styles' => $styles,
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
        
        $artists = Artist::all()->where('env', config('app.env'));

        $attributes['order'] = count($artists) + 1;
        
        $artist = Artist::create($attributes);
    }

    public function update(Artist $artist){
        
        $validated = request()->validate([
            'name' => ['required'],
            'label_id' => ['required'],
            'desktop.image' => ['image', File::types('jpg', 'jpeg', 'png')],
            'desktop.image_position' => ['nullable'],
            'desktop.image_use_custom_position' => ['required'],
            'desktop.image_custom_position_x' => ['exclude_unless:desktop.image_use_custom_position,1', 'nullable'],
            'desktop.image_custom_position_y' => ['exclude_unless:desktop.image_use_custom_position,1', 'nullable'],
            'mobile.image' => ['image', File::types('jpg', 'jpeg', 'png')],
            'mobile.image_position' => ['nullable'],
            'mobile.image_use_custom_position' => ['required'],
            'mobile.image_custom_position_x' => ['exclude_unless:mobile.image_use_custom_position,1', 'nullable'],
            'mobile.image_custom_position_y' => ['exclude_unless:mobile.image_use_custom_position,1', 'nullable'],
            'url' => ['required'],
            'token' => ['nullable'],
            'about' => ['required'],
        ]);

        $date = date('siH_d_m_y');

        $name = str_replace(' ', '-', $validated['name']);
        $name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);

        $devices = ['desktop', 'mobile'];
        $deviceAttributesMap = [];
        foreach ($devices as $device){
            if ($validated[$device]['image_use_custom_position'] == 0){
                $validated[$device]['image_custom_position_x'] = null;
                $validated[$device]['image_custom_position_y'] = null;
            }

            unset($validated[$device]['image_use_custom_position']);

            $deviceAttributesMap[$device] = $validated[$device];

            if(isset($deviceAttributesMap[$device]['image'])){
                $type = '.' . explode('/', request()->file($device . '.image')->getClientMimeType())[1];

                $dir = '/' . $artist->slug . '_' . $device . '_background_' . $date;

                $file = request()->file($device . '.image');

                $images = [];

                $image = Image::fromFile($file);
                $imageToEdit = Image::fromFile($file);
                
                $images['default'] = $image;

                $images['medium'] = $imageToEdit->resize(500, 500);

                $directories = Storage::disk('public')->directories();

                if (!in_array('uploaded', $directories)){
                    Storage::disk('public')->makeDirectory('uploaded');
                }

                $images['default']->save(config('filesystems.disks.public.root') . '/uploaded/default' . $type);
                $images['medium']->save(config('filesystems.disks.public.root') . '/uploaded/medium' . $type);

                $defaultContents = Storage::disk('public')->get('uploaded/default' . $type);
                $mediumContents = Storage::disk('public')->get('uploaded/medium' . $type);

                $name = 'default' . $type;
                $uploaded = Storage::put(config('app.live-site-url') . '/uploads' . $dir . '/' . $name, $defaultContents);

                $name = 'medium' . $type;
                $uploaded = Storage::put(config('app.live-site-url') . '/uploads' . $dir . '/' . $name, $mediumContents);

                if ($uploaded){
                    $deviceAttributesMap[$device]['image'] = config('filesystems.disks.spaces.url') . '/uploads' . $dir;
                    $deviceAttributesMap[$device]['image_extension'] = $type;
                    Storage::disk('public')->delete('uploaded/default' . $type);
                    Storage::disk('public')->delete('uploaded/medium' . $type);
                }
            }

        }

        if (isset($validated['token'])){
            $validated['music_requires_refresh'] = true;
            $validated['posts_requires_refresh'] = true;
            $validated['videos_requires_refresh'] = true;
            $validated['design_requires_refresh'] = true;
        }

        $previousImageData = null;

        foreach ($deviceAttributesMap as $device => $attributes) {

            // Get existing style for this device (current env by default)
            $existingStyle = $artist->pageStyleForDevice($device)->first();

            $hasNewImage = !empty($attributes['image']);
            $hasExistingImage = $existingStyle && $existingStyle->image;

            // If no new image AND no existing image → fallback to previous device
            if (!$hasNewImage && !$hasExistingImage && $previousImageData) {
                $attributes['image'] = $previousImageData['image'] ?? null;
                $attributes['image_extension'] = $previousImageData['image_extension'] ?? null;
            }

            if ($attributes) {
                $artist->pageStyleForDevice($device)->update($attributes);
            }

            // Update previous image tracker (priority: new > existing > fallback)
            if (!empty($attributes['image'])) {
                $previousImageData = [
                    'image' => $attributes['image'],
                    'image_extension' => $attributes['image_extension'] ?? null,
                ];
            } elseif ($hasExistingImage) {
                $previousImageData = [
                    'image' => $existingStyle->image,
                    'image_extension' => $existingStyle->image_extension,
                ];
            }
        }

        $artist->update($validated);

        foreach ($deviceAttributesMap as $device => $attributes) {
            $attributes['env'] = config('app.env');
            if ($attributes) {
                $artist->pageStyleForDevice($device)->update($attributes);
            }
        }

        return redirect('/' . $artist->slug)->with('success', 'Artist Updated');
    }

    public function push(Artist $artist){

        return redirect('/' . $artist->slug)->with('success', 'Artist Updated');
    }
}
