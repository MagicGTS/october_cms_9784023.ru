<?php
namespace T9784023\Cources\Models;

use Seeder;
use \T9784023\Images\Models\Image;
use \T9784023\Images\Models\ImagesList;
use \T9784023\LearningPrograms\Models\Program;
use \T9784023\Schedules\Models\Schedule;

class SeedHighAltitudeWorksTables extends Seeder
{
    public function run()
    {
        $images = [
            new Image([ // 0

                'path' => 'img/background.png',
                'mime' => 'image/png',
            ]),
            new Image([ // 1

                'path' => 'img/circle.png',
                'mime' => 'image/png',
            ]),
        ];
        $image_list =
        new ImagesList([ // 0
            'title' => 'Обучение по пожарной безопасности',
            'slug' => 'Оппб',
        ]);
        foreach ($images as $image) {
            $image->save();
        }
        $image_list->save();
        $image_list->images()->attach($images[0], ['tag' => 'backgroundcon']);
        $image_list->images()->attach($images[1], ['tag' => 'circle']);
         $program = new Program([
            'hours' => 5,
            'name' => 'хоть что-то',
        ]);
        $program->save();
        $schedule = new Schedule([
            'begin_at' => "2022-07-09 10:00",
            'location' => 'каб 5',
            'url' => 'http://ya.ru',
        ]);
        $schedule->save();
        $HighAltitudeWorks = new HighAltitudeWorks([
            'cost' => 5000,
            'description' => 'описание, довольно длинное',
            'hours' => 8,
            'learning_form' => 'очное',
            'fines_description' => 'большие и серьезные штрафы',
            'fines_links' => 'http://ya.ru',
            'whom_needs' => 'точно вам нужно',
            'what_will_learn' => 'все что-бы не налететь на штрафы',
            'schedule_description' => 'как минимум раз в год',
            'howto_sign_up' => 'сами разберетесь, мне еще и тут инструкции писать?',
            'result_img_folder' => 'folder6',
            'schould_to_know' => 'в этот раз тут могла быть важная информация',
            'youtube_url' => 'http://youtube.com',
        ]);
        $HighAltitudeWorks->img_background()->attach($images[0]);
        $HighAltitudeWorks->img_rounded()->attach($images[1]);
        $HighAltitudeWorks->schedule()->attach($schedule);
        $HighAltitudeWorks->program()->attach($program);
    }
}
