<?php

namespace App\Classes;

use App\Models\Artist;

class RefreshArtist
{
    protected $artist;

    public function __construct(Artist $artist)
    {
        $this->artist = $artist;    
    }

    public function fetchMusic(){
        $url = $this->artist->url;
        $token = $this->artist->token;

        if ($token){
            $fetchedMusic = $this->runCurl($url . "/api/" . $token . "/music");

            $currentMusic = $this->artist->music;

            foreach ($fetchedMusic as $item){
                $exists = $currentMusic->contains('external_site_item_id', $item->id);
                
                if ($exists){

                    $oldMusic = $currentMusic->where('external_site_music_id', $item->id)->first();
                    $fetchedTime = strtotime($item->updated_at);
                    $oldTime = strtotime($oldMusic->updated_at->toDateTimeString());
                    if ($fetchedTime != $oldTime){
                        $attributes = (array) $item;

                        unset($attributes['id']);
                        unset($attributes['staging_music_id']);
                        unset($attributes['api_access']);
                        unset($attributes['available_for_meta_data']);
                        
                        $oldMusic->update($attributes);
                    }
                } else {
                    $attributes = (array) $item;
                    
                    unset($attributes['id']);
                    unset($attributes['staging_music_id']);
                    unset($attributes['api_access']);
                    unset($attributes['available_for_meta_data']);

                    $attributes['external_site_music_id'] = $item->id;
                    $attributes['from_api'] = true;

                    // Music::create($attributes);

                    if (isset($attributes['released'])){
                        $attributes['release_date'] = $attributes['released'];
                        unset($attributes['released']);
                    }
                    if (isset($attributes['presave'])){
                        $attributes['presave_date'] = $attributes['presave'];
                        unset($attributes['presave']);
                    }

                    $this->artist->music()->create($attributes);
                }
            }
        }
    }

    public function fetchPosts(){
        $url = $this->artist->url;
        $token = $this->artist->token;

        if ($token){
            $fetchedPosts = $this->runCurl($url . "/api/" . $token . "/news");

            $currentPosts = $this->artist->posts;

            foreach ($fetchedPosts as $item){
                $exists = $currentPosts->contains('slug', $item->slug);
                    
                if ($exists){
                    $oldPost = $currentPosts->where('slug', $item->slug)->first();
                    $fetchedTime = strtotime($item->updated_at);
                    $oldTime = strtotime($oldPost->updated_at->toDateTimeString());
                    if ($fetchedTime != $oldTime){
                        $attributes = (array) $item;

                        unset($attributes['id']);
                        unset($attributes['music_id']);
                        unset($attributes['video_id']);
                        unset($attributes['api_access']);
                        
                        $oldPost->update($attributes);
                    }
                } else {
                    $attributes = (array) $item;
                    
                    if ($attributes['video_id']){
                        $videoId = $attributes['image'];

                        $thumbnail = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

                        $attributes['image'] = $thumbnail;
                    }

                    unset($attributes['id']);
                    unset($attributes['music_id']);
                    unset($attributes['video_id']);
                    unset($attributes['api_access']);

                    $attributes['slug'] = $item->slug;
                    $attributes['from_api'] = true;

                    $this->artist->posts()->create($attributes);
                }
            }
        }
        
    }

    protected function runCurl(String $url){

        $response = 'initialize';

            $ch = curl_init();
            //$response = curl_exec($ch);

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);

            if (curl_error($ch)){
                $response = 'error';
            }

            curl_close($ch);

            return json_decode($response);

    }

}
