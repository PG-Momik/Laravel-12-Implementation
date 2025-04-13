<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Services\WebzApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebzPostsController extends Controller
{
    public function __construct(private readonly WebzApiService $webzApiService){}


    public function index(Request $request): JsonResponse
    {
        try {
            $this->webzApiService->fetchPosts($request->toArray(), function (int $retrieved, int $remaining) {
                    $logMessage = "Progress: {$retrieved} fetched, {$remaining} remaining";
                    logger()->info($logMessage);
                }
            );

            return response()->json(['message' => 'Posts fetched successfully.']);
        }catch (Exception $exception){
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}