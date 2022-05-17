<?php
namespace T9784023\Cources\Models;

use Seeder;
use \T9784023\Images\Models\Image;
use \T9784023\Images\Models\ImagesList;

class SeedHighAltitudeWorksTables extends Seeder
{
    public function run()
    {
        $images = [
            new Image([ // 0

                'path' => 'img/ico_ОХРАНА ТРУДА.png',
                'mime' => 'image/png',
            ]),
        ];
        $image_lists = [
            new ImagesList([ // 0
                'title' => 'Обучение по пожарной безопасности',
                'slug' => 'Оппб',
            ]),
        ];
        foreach ($images as $image) {
            $image->save();
        }

        foreach ($image_lists as $key => $il) {
            $il->save();
            $il->images()->attach($images[$key], ['tag' => 'icon']);
        }

    }
}
