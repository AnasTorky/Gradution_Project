<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitiesTableSeeder extends Seeder
{
    public function run()
    {
        Activity::create([
            'category_id' => 1,
            'name' => 'Play-based Learning',
            'description' => 'Activities that engage children in learning through play.',
            'content' => 'Interactive games, storytelling, and role-playing activities.',
            'photo' => 'play_based_learning.jpg',
        ]);

        Activity::create([
            'category_id' => 2,
            'name' => 'Speech Therapy',
            'description' => 'Activities focused on improving communication and speech skills.',
            'content' => 'Speech exercises and communication-focused games.',
            'photo' => 'speech_therapy.jpg',
        ]);
        Activity::create([
            'category_id' => 3,
            'name' => 'game',
            'description' => 'Activities that engage children in learning through play.',
            'content' => 'Interactive games, storytelling, and role-playing activities.',
            'photo' => 'play_based_learning.jpg',
        ]);




        // Add more static activities as needed
    }
}
