<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $fillable = ['username'];
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function getPostReport()
    {
        $posts = $this->posts();
        $latest = $posts->orderBy('created_at', 'desc')->first();
        return [
            'username' => $this->username,
            'total_posts_count' => $posts->count(),
            'last_post_title' => $latest->title,
        ];
    }
}
