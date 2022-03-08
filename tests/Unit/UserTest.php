<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Service\UserService;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function testTopAuthors()
    {
        /*
         * Scenario:
         * - User 1 posted 2 posts 3 days ago, 1 post recently
         * - User 2 posted 4 posts 4 days ago, 1 post recently
         * - User 3 posted 6 posts 5 days ago, 1 post recently
         */
        $threeDaysAgo = Carbon::now()->subDays(3);
        $fourDaysAgo = Carbon::now()->subDays(4);
        $fiveDaysAgo = Carbon::now()->subDays(5);
        $user1 = User::factory()->has(Post::factory(['created_at' => $threeDaysAgo])->count(2))->create();
        $user2 = User::factory()->has(Post::factory(['created_at' => $fourDaysAgo])->count(4))->create();
        $user3 = User::factory()->has(Post::factory(['created_at' => $fiveDaysAgo])->count(6))->create();

        $user1LastTitle = 'Post 1';
        $user2LastTitle = 'Post 2';
        $user3LastTitle = 'Post 3';

        $user1LastPost = Post::factory([
            'user_id' => $user1->id,
            'title' => $user1LastTitle,
        ])->create();
        $user2LastPost = Post::factory([
            'user_id' => $user2->id,
            'title' => $user2LastTitle,
        ])->create();
        $user3LastPost = Post::factory([
            'user_id' => $user3->id,
            'title' => $user3LastTitle,
        ])->create();

        $userService = new UserService();
        // Getting top authors which created more than 10 posts in last 7 days should return empty data
        $result = $userService->getTopAuthors(10, 7);
        $this->assertEmpty($result);

        // Getting top authors which created more than 6 posts in last 7 days should return User 3
        $result = $userService->getTopAuthors(6, 7);
        $this->assertEquals(1, count($result));
        $this->assertEquals($user3LastTitle, $result[0]['last_post_title']);
        $this->assertEquals($user3->username, $result[0]['username']);

        // Getting top authors which created more than 4 posts in last 7 days should return User 1 and User 2
        $result = $userService->getTopAuthors(4, 7);
        $this->assertEquals(2, count($result));

        // Getting top authors which created more than 2 posts in last 7 days should return 3 users
        $result = $userService->getTopAuthors(2, 7);
        $this->assertEquals(3, count($result));

        // Getting top authors which created more than 3 posts in last 4 days should return User 2
        $result = $userService->getTopAuthors(3, 4);
        $this->assertEquals(1, count($result));
        $this->assertEquals($user2->username, $result[0]['username']);
    }
}
