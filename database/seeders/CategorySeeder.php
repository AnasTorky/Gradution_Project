<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'name' => 'Videos-based Learning',
            'description' => 'Videos that engage children to learn',
            'content' => 'Storytelling Videos.',
            'photo' => 'play_based_learning.jpg',
        ]);

        Category::create([
            'name' => 'Communication',
            'description' => 'Activities focused on improving communication and speech skills.',
            'content' => 'Speech exercises and communication-focused games.',
            'photo' => 'speech_therapy.jpg',
        ]);
        Category::create([
            'name' => 'Games',
            'description' => 'Activities that engage children in learning through play.',
            'content' => 'Interactive games, storytelling, and role-playing activities.',
            'photo' => 'play_based_learning.jpg',
        ]);
    }
}
