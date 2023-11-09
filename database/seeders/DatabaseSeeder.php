<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Note;
use App\Models\NoteTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    User::factory()->count(20)
        ->has(
            Note::factory()->count(5)
            ->has(
                NoteTag::factory()->count(2)
                    ->for(
                        Tag::factory()
                    )
                )
            )
        ->create();
    }
}
