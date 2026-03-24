<?php

namespace App\Http\Controllers;

use App\Classes\TimeConverter;
use App\Classes\UniversalData;
use App\Models\Artist;
use App\Models\Music;
use App\Models\Post;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Artist $artist){
    
        $universalData = new UniversalData();

        $artist->load(['posts' => function ($query) {
            $query->where(function ($q) {
                $q->where('env', config('app.env'))
                ->orWhereNotNull('external_site_id');
            });
        }]);

        return view('posts.index', [
            'universalData' => $universalData,
            'artist' => $artist,
        ]);
    }

    public function show(Artist $artist, Post $post){

        $timeConverter = new TimeConverter();
        $publishedTime = $timeConverter->ConvertTimeToCentral($post->published_time);
        $currentTime = $timeConverter->GetCurrentCentralTime();

        if ($publishedTime > $currentTime) {
            if (Auth::guest()){
                return redirect('error');
            }
        }

        $universalData = new UniversalData();

        if(str_contains($post->body, 'bullet')) {
            $allListArray = explode("<bullet>", $post->body); //Splitting into arrays at <bullet> tags. Content - listcontent</list>contentafter - listcontent</list>contentafter
            $bodyString = $allListArray[0]; //set content before lists appear
            for($i = 1; $i < count($allListArray); $i++){
                $newArray = explode("</bullet>", $allListArray[$i]); //Split into list (index 0) and after list (all indices after list);
                $listArray = explode("\r\n", $newArray[0]);
                $bodyString .= '<ul class="ml-4 list-disc">';
                for($index = 1; $index < count($listArray) - 1; $index++){
                    if (strlen($listArray[$index]) > 1){
                        $bodyString .= "<li>" . $listArray[$index] . "</li>";
                    }
                }
                $bodyString .= '</ul>' . $newArray[1];
            }
            $post->body = $bodyString;
        }

        if (str_contains($post->body, 'lyric')){
            $allListArray = explode("<lyric>", $post->body); //Splitting into arrays at <bullet> tags. Content - listcontent</list>contentafter - listcontent</list>contentafter
            $bodyString = $allListArray[0]; //set content before lists appear
            for($i = 1; $i < count($allListArray); $i++){
                $newArray = explode("</lyric>", $allListArray[$i]); //Split into list (index 0) and after list (all indices after list);
                $listArray = explode("\r\n", $newArray[0]);
                $bodyString .= '<ul class="flex flex-col italic list-none">';
                for($index = 1; $index < count($listArray) - 1; $index++){
                    if (strlen($listArray[$index]) > 1){
                        $bodyString .= '<li>' . $listArray[$index] . "</li>";
                    }
                }
                $bodyString .= '</ul>' . $newArray[1];
            }
            $post->body = $bodyString;
        }

        if (str_contains($post->body, '<tour>')){
            $allListArray = explode("<tour>", $post->body);
            $bodyString = $allListArray[0];
            for($i = 1; $i < count($allListArray); $i++){
                $newArray = explode("</tour>", $allListArray[$i]);
                $listArray = explode("\r\n", $newArray[0]);
                $bodyString .= '<ul class="flex flex-col list-none">';
                for($index = 1; $index < count($listArray) - 1; $index++){
                    if (strlen($listArray[$index]) > 1){
                        $bodyString .= '<li>' . $listArray[$index] . "</li>";
                    }
                }
                $bodyString .= '</ul>' . $newArray[1];
            }
            $post->body = $bodyString;
        }

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

        if ($post->video_id){
            $videoId = $post->image; // your YouTube video ID
            $thumbnail = "https://img.youtube.com/vi/$videoId/maxresdefault.jpg";

            $post->image = $thumbnail;
        } else {
            $post->image = config('filesystems.disks.spaces.url') . $post->image;
        }

        return view('posts.show', [
            'universalData' => $universalData,
            'post' => $post,
            'artist' => $artist,
        ]);
    }

    public function create(Artist $artist){
    
        $universalData = new UniversalData();

        $artist->load('music', 'videos');

        return view('posts.create', [
            'universalData' => $universalData,
            'artist' => $artist,
        ]);
    }

    public function edit(Artist $artist, Post $post){
    
        $universalData = new UniversalData();

        $artist->load('music', 'videos');

        return view('posts.edit', [
            'universalData' => $universalData,
            'artist' => $artist,
            'post' => $post,
        ]);
    }

    public function store(Artist $artist){

        $attributes = request()->validate([
            'title' => ['required', 'max:255'],
            'subtitle_one' => ['max:255', 'nullable'],
            'subtitle_two' => ['max:255', 'nullable'],
            'linked_item' => ['required'],
            'music_id' => ['exclude_unless:linked_item,music', 'required_if:linked_item,music', 'nullable'],
            'video_id' => ['exclude_unless:linked_item,video', 'required_if:linked_item,video', 'nullable'],
            'image' => ['required_if:linked_item,none', 'file', 'mimes:png,jpg', 'max:2048'],
            'image_alt_text' => ['nullable'],
            'image_position' => ['required_if:linked_item,none'],
            'body' => ['required'],
            'publish_date' => ['required', 'date'],
            'publish_time' => ['required'],
            'is_production' => ['required', 'boolean'],
        ]);

        unset($attributes['linked_item']);

        $pushImmediate = $attributes['is_production'];

        $date = date('siH_d_m_y');

        $title = str_replace(' ', '-', $attributes['title']);
        $title = preg_replace('/\<link=".*?\">/', '', $title);
        $title = str_replace('</link>', '', $title);
        $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);

        $titleArray = explode('-', $title);

        $count = 0;
        $slug = '';

        foreach($titleArray as $word){
            $slug .= strtolower($word);
            $count++;
            if ( $count <= 7 && $count < count($titleArray)){
                $slug .= '-';
            } else {
                if (count(Post::where('slug', $slug)->get()) > 0){
                    if ($count < count($titleArray)){
                        $slug .= '-';
                    } else {
                        $slug = $slug . '-' . $this->getNewSlugCount($slug);
                    }
                } else {
                    break;
                }
            }
        }

        $attributes['slug'] = $slug;

        $attributes['publish_date'] = date_create($attributes['publish_date'] . ' ' . $attributes['publish_time']);

        if(isset($attributes['image'])){
            $type = explode('/', request()->file('image')->getClientMimeType())[1];
            $name = 'news-upload-' . $date . '.' . $type;

            $uploaded = Storage::putFileAs(config('app.live-site-url') . '/uploads/news', request()->file('image'), $name, 'public');

            if ($uploaded){
                $attributes['image'] = '/uploads/news/' . $name;
            }
        } else {

            if (isset($attributes['music_id'])){
                $music = Music::where('id', $attributes['music_id'])->first();
                $attributes['image'] = $music->artwork;
            }

            if (isset($attributes['video_id'])){
                $video = Video::where('id', $attributes['video_id'])->first();
                
                $videoString = $video->link;

                $urlArray = explode('?v=', $videoString);
                if (str_contains($urlArray[count($urlArray)-1], '&')){
                    $appendedUrlArray = explode('&', $urlArray[count($urlArray)-1]);
                    $urlArray[count($urlArray)-1] = $appendedUrlArray[0];
                }

                $videoId = $urlArray[count($urlArray)-1];

                $attributes['image'] = $videoId;
            }
        }

        unset($attributes['publish_time']);
        unset($attributes['is_production']);

        $attributes['env'] = config('app.env');

        $artist->posts()->create($attributes);

        if (config('app.env') !== 'production' && $pushImmediate){
            $attributes['env'] = 'production';
            $artist->posts()->create($attributes);

        }

        return redirect('/admin-' . config('app.initials') . '/artists/' . $artist->slug . '/news')->with('success', 'News Added');

    }

    public function update(Artist $artist, Post $post){
        
    }

    public function push(Artist $artist){

        if(config('app.env') == 'production'){
            return back()->with('failure', 'Cannot push from live site!');
        }

        foreach ($artist->posts->where('external_site_id', null) as $post){
            $productionPost = Post::where('env', 'production')->where('slug', $post->slug)->first();

            if ($productionPost){
                $attributes = $post->toArray();
                unset($attributes['id']);
                unset($attributes['created_at']);
                unset($attributes['updated_at']);
                unset($attributes['env']);
                $productionPost->update($attributes);
            } else {
                $attributes = $post->toArray();
                unset($attributes['id']);
                unset($attributes['created_at']);
                unset($attributes['updated_at']);
                $attributes['env'] = 'production';
                Post::create($attributes);
            }
        }

        return back()->with('success', 'All news set live!');
    }

    protected function getNewSlugCount(string $slug) {
        $count = 1;

        while ($this->checkSlugExists($slug, $count)){
            $count++;
            if ($count > 10){
                break;
            }
        }
        return $count;
    }

    protected function checkSlugExists(string $slug, int $count){

        return count(Post::where('slug', $slug . ($count > 1 ? '-' . $count : ''))->where('env', config('app.env'))->get()) > 0;

    }
}
