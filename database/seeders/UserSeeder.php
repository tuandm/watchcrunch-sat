<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 10000) as $userId) {
            $user = \App\Models\User::create([
                'username' => 'username_' . $userId,
            ]);
        }
    }
}
