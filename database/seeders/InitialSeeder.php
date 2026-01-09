<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        
        \App\Models\Label::create([
            'name' => 'Quartz Hill Music Group',
            'is_sublabel' => false,
        ]);

        $qhr = \App\Models\Label::create([
            'name' => 'Quartz Hill Records',
            'sublabel_order' => 1,
        ]);

        $scr = \App\Models\Label::create([
            'name' => 'Stone Country Records',
            'sublabel_order' => 2,
        ]);

        $artists = [
            [
                'name' => 'Joe Nichols',
                'url' => 'https://joenichols.com',
                'label_id' => $qhr->id,
            ],
            [
                'name' => 'Matt Cooper',
                'url' => 'https://mattcoopermusic.com',
                'label_id' => $qhr->id,
            ],
            [
                'name' => 'Ben Gallaher',
                'url' => 'https://bengallaher.com',
                'label_id' => $scr->id,
            ],
            [
                'name' => 'Lakelin Lemmings',
                'url' => 'https://lakelinlemmings.com',
                'label_id' => $qhr->id,
            ],
            [
                'name' => 'Spencer Hatcher',
                'url' => 'https://spencerhatchermusic.com/',
                'label_id' => $scr->id,
            ],
            [
                'name' => '2 Lane Summer',
                'url' => 'https://2lanesummer.com',
                'label_id' => $qhr->id,
            ],
            [
                'name' => 'Annie Bosko',
                'url' => 'https://anniebosko.com',
                'label_id' => $scr->id,
            ],
            [
                'name' => 'Dusty Black',
                'url' => 'https://dustyblackmusic.com',
                'label_id' => $scr->id,
            ],
        ];

        $order = 1;

        foreach($artists as $artist){
            \App\Models\Artist::create([
                'label_id' => $artist['label_id'],
                'name' => $artist['name'],
                'url' => $artist['url'],
                'order' => $order++,
            ]);
        }
    }
}
