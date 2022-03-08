<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function topAuthors(Request $request)
    {
        $minPost = $request->post('min_post', 10);
        $periodInDay = $request->post('period', 7);
        $topAuthors = $this->userService->getTopAuthors($minPost, $periodInDay);
        return response()->json($topAuthors);
    }
}
