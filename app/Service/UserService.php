<?php

namespace App\Service;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

/**
 * User Service
 * @package App\Service
 */
class UserService
{
    /**
     * @param int $minPost
     * @param int $periodInDay
     * @return array
     */
    public function getTopAuthors(int $minPost = 30, int $periodInDay = 7): array
    {
        $date = \Carbon\Carbon::now()->subDays($periodInDay);
        $users = collect();
        try {
            // Get top authors with number of posts in $withinDays days
            $topAuthorsWithPostTotal = Post::query()->select(DB::raw('count(user_id) as post_num, user_id'))
                ->groupBy('user_id')
                ->where('created_at', '>=', $date)
                ->havingRaw('count(user_id) > ?', [$minPost])
                ->orderBy('post_num', 'DESC')
                ->get()
                ->pluck('post_num', 'user_id');


            // Chunking the top authors to avoid limitation of PDO. 10000 is just a number, can be put to better place
            // @see https://stackoverflow.com/questions/40361164/pdoexception-sqlstatehy000-general-error-7-number-of-parameters-must-be-bet
            foreach ($topAuthorsWithPostTotal->chunk(10000) as $chunkedUserIds) {
                // Get user info and latest post
                $chunkedUsers = User::query()->select('users.id as user_id', 'username', 'posts.title')
                    ->leftJoin('posts', function($query) {
                        $query->on('users.id', '=', 'posts.user_id')
                            ->whereRaw('posts.id IN (SELECT MAX(p2.id) FROM posts AS p2 JOIN users AS u2 ON u2.id = p2.user_id GROUP BY u2.id)');
                    })
                    ->whereIn('users.id', $chunkedUserIds->keys()->toArray())
                    ->orderBy('posts.created_at', 'ASC')
                    ->get();

                // Merge post data with number of posts to avoid extra query
                foreach ($chunkedUsers as $user) {
                    $users->add([
                        'username' => $user->username,
                        'total_posts_count' => $chunkedUserIds->get($user->user_id),
                        'last_post_title' => $user->title,
                    ]);
                }
            }
        } catch (\Exception $exception) {
            \Log::error('Error occurred: ' . $exception->getMessage());
            \Log::error($exception->getTraceAsString());
        }

        return $users->toArray();
    }
}
