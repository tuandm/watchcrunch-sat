<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('max_execution_time', 3600);
        $maxUserId = User::query()->max('id');
        foreach (range(1, 200000) as $postId) {
            $date = Carbon::now();
            $rand = rand(1, 30);
            $date->modify('+' . $rand . ' days');
            $user = \App\Models\Post::create([
                'title' => 'Post Title ' . $postId,
                'user_id' => rand(1, $maxUserId),
                'created_at' => $date->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
