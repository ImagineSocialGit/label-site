<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\PageStyle;
use Illuminate\Http\Request;

class PageStyleController extends Controller
{
    public function push(Artist $artist){
    
        $sourceEnv = config('app.env');
        $targetEnv = 'production';

        // Fetch all page styles for the artist in the source environment
        $sourceStyles = $artist->pageStyles($sourceEnv)->get();

        foreach ($sourceStyles as $style) {
            // Find existing style for the same device in the target env, or create a new one
            $targetStyle = $artist->pageStyleForDevice($style->device_type, $targetEnv)->first();

            if (!$targetStyle) {
                $targetStyle = new PageStyle();
                $targetStyle->artist_id = $artist->id;
                $targetStyle->device_type = $style->device_type;
                $targetStyle->env = $targetEnv;
            }

            // Copy all relevant fields from source to target
            $targetStyle->image = $style->image;
            $targetStyle->image_position = $style->image_position;
            $targetStyle->image_custom_position_x = $style->image_custom_position_x;
            $targetStyle->image_custom_position_y = $style->image_custom_position_y;
            $targetStyle->image_extension = $style->image_extension;
            $targetStyle->from_api = $style->from_api;

            $targetStyle->save();
        }

        return back()->with('success', 'Artist style pushed to production');
    }
}
