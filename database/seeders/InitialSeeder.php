<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\SeederInfo\LabelData;
use Database\Seeders\SeederInfo\ArtistData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InitialSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        \App\Models\SiteSettings::create();

        \App\Models\User::create([
            'user' => config('auth.admin.new-user'),
            'password' => config('auth.admin.new-user-password'),
            'permissions' => config('auth.admin.new-user-permissions'),
        ]);

        $labelData = new LabelData();

        foreach($labelData->labels as $label){
            \App\Models\Label::create($label);
        }

        $artistData = new ArtistData();

        $order = 1;

        $devices = ['desktop', 'mobile'];

        foreach($artistData->artists as $artist){
            $slug = str_replace(' ', '-', $artist['name']);
            $slug = strtolower($slug);
            $newArtist = \App\Models\Artist::create([
                'label_id' => $artist['label_id'],
                'name' => $artist['name'],
                'slug' => $slug,
                'about' => array_key_exists('about', $artist) ? $artist['about'] : null,
                'token' => array_key_exists('token', $artist) ? $artist['token'] : null,
                'music_requires_refresh' => array_key_exists('music_requires_refresh', $artist) ? true : false,
                'posts_requires_refresh' => array_key_exists('posts_requires_refresh', $artist) ? true : false,
                'videos_requires_refresh' => array_key_exists('videos_requires_refresh', $artist) ? true : false,
                'design_requires_refresh' => array_key_exists('design_requires_refresh', $artist) ? true : false,
                'url' => $artist['url'],
                'order' => $order++,
            ]);

            foreach($devices as $device){
                $newArtist->pageStyles()->create([
                    'device_type' => $device
                ]);

                $newArtist->pageStyles()->create([
                    'device_type' => $device,
                    'env' => 'production'
                ]);
            }
        }
    }
}
