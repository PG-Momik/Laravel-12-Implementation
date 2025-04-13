<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostsController extends Controller
{
    public function __construct(private readonly PostService $postService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        try {

            // TODO: Add request validation here or above.
            $filters = $request->only(
                ['q', 'author', 'language', 'sentiment', 'ai_allow', 'webz_reporter', 'published', 'crawled']
            );

            $posts = $this->postService->getPaginatedPosts(
                filters: $filters,
                perPage: $request->input('per_page', 15)
            );

            return response()->json(
                [
                    'success' => true,
                    'data'    => JsonResource::collection($posts)
                ]
            );
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => false,
                    'errors'  => $exception->getMessage()
                ],
                500
            );
        }
    }
}
