<?php

namespace App\Services;

use App\Models\Artist;
use App\Models\PageStyle;
use Illuminate\Support\Collection;

class PageStyleService
{
    protected Artist $artist;

    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getStylesByDevice(): array
    {
        $styles = [];

        $grouped = $this->artist
            ->pageStyles(config('app.env'))
            ->orderBy('id')
            ->get()
            ->groupBy('device_type');

        foreach ($grouped as $device => $deviceStyles) {
            $style = $this->preferredStyle($deviceStyles);

            if (! $style) {
                continue;
            }

            if ($style->image_custom_position_x !== null
                && $style->image_custom_position_y !== null
            ) {
                $styles[$device] = [
                    'image' => $style->image,
                    'styleAttribute' => 'object-position: '
                        .$style->image_custom_position_x.'% '
                        .$style->image_custom_position_y.'%;',
                    'extension' => $style->image_extension,
                ];

                continue;
            }

            $styles[$device] = [
                'image' => $style->image,
                'position' => $style->image_position,
                'extension' => $style->image_extension,
            ];
        }

        return $styles;
    }

    /**
     * Generate responsive CSS for breakpoints.
     *
     * @param  array<string, string>  $breakpoints
     */
    public function generateResponsiveCss(array $breakpoints): string
    {
        $css = '';

        foreach ($breakpoints as $device => $width) {
            $style = $this->preferredStyle(
                $this->artist
                    ->pageStyles(config('app.env'))
                    ->where('device_type', $device)
                    ->orderBy('id')
                    ->get(),
            );

            if (! $style || ! $style->image) {
                continue;
            }

            $position = $style->image_position ?: 'object-center';

            $css .= "@media (max-width: {$width}) { \n";
            $css .= "  .artist-background { \n";
            $css .= "    background-image: url('{$style->image}'); \n";
            $css .= "    background-position: {$position}; \n";
            $css .= "  } \n";
            $css .= "} \n";
        }

        return $css;
    }

    /**
     * @param  Collection<int, PageStyle>  $styles
     */
    private function preferredStyle(Collection $styles): ?PageStyle
    {
        if ((bool) $this->artist->syndication_enabled) {
            $sourceStyle = $styles->first(
                static fn (PageStyle $style): bool => (bool) $style->from_api,
            );

            if ($sourceStyle) {
                return $sourceStyle;
            }
        }

        return $styles->first(
            static fn (PageStyle $style): bool => ! (bool) $style->from_api,
        ) ?? $styles->first();
    }
}