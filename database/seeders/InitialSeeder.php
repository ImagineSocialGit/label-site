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

        foreach($artistData->artists as $artist){
            $slug = str_replace(' ', '-', $artist['name']);
            $slug = strtolower($slug);
            \App\Models\Artist::create([
                'label_id' => $artist['label_id'],
                'name' => $artist['name'],
                'slug' => $slug,
                'url' => $artist['url'],
                'order' => $order++,
            ]);
        }
    }
}
