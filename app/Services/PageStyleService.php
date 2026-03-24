<?php

namespace App\Services;

use App\Models\Artist;

class PageStyleService
{
    protected Artist $artist;

    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    /**
     * Get page styles indexed by device
     */
    public function getStylesByDevice(): array
    {
        $styles = [];

        foreach ($this->artist->pageStyles(config('app.env'))->get() as $style) {
            
            if ($style->image_custom_position_x != null && $style->image_custom_position_y != null){
                
                $styleAttribute = 'object-position: ' .
                    $style->image_custom_position_x . '% ' .
                    $style->image_custom_position_y . '%;';

                $styles[$style->device_type] = [
                    'image' => $style->image,
                    'styleAttribute' => $styleAttribute,
                    'extension' => $style->image_extension,
                ];

            } else {
                $styles[$style->device_type] = [
                    'image' => $style->image,
                    'position' => $style->image_position,
                    'extension' => $style->image_extension,
                ];
            }
        }

        return $styles;
    }

    /**
     * Generate responsive CSS for breakpoints
     * @param array $breakpoints associative array like ['mobile' => '480px', 'tablet' => '768px', 'desktop' => '1024px']
     */
    public function generateResponsiveCss(array $breakpoints): string
    {
        $css = '';

        foreach ($breakpoints as $device => $width) {
            $style = $this->artist->pageStyleForDevice($device);

            if ($style) {
                $position = $style->cssPosition();
                $image = $style->image;

                $css .= "@media (max-width: {$width}) { \n";
                $css .= "  .artist-background { \n";
                $css .= "    background-image: url('{$image}'); \n";
                $css .= "    background-position: {$position}; \n";
                $css .= "  } \n";
                $css .= "} \n";
            }
        }

        return $css;
    }
}