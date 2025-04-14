<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitiesTableSeeder extends Seeder
{
    public function run()
    {
        Activity::create([
            'name' => 'Play-based Learning',
            'description' => 'Activities that engage children in learning through play.',
            'content' => 'Interactive games, storytelling, and role-playing activities.',
            'category' => 'Social Skills',
            'photo' => 'play_based_learning.jpg',
        ]);

        Activity::create([
            'name' => 'Speech Therapy',
            'description' => 'Activities focused on improving communication and speech skills.',
            'content' => 'Speech exercises and communication-focused games.',
            'category' => 'Speech Development',
            'photo' => 'speech_therapy.jpg',
        ]);
        Activity::create([
            'name' => 'game',
            'description' => 'Activities that engage children in learning through play.',
            'content' => 'Interactive games, storytelling, and role-playing activities.',
            'category' => 'Games Skills',
            'photo' => 'play_based_learning.jpg',
        ]);

        Activity::create([
            'name' => 'Speech Therapy',
            'description' => 'Activities focused on improving communication and speech skills.',
            'content' => 'Speech exercises and communication-focused games.',
            'category' => 'communication',
            'photo' => 'speech_therapy.jpg',
        ]);


        // Add more static activities as needed
    }
}
