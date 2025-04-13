<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\WebzQueryBuilder;
use App\DTO\WebzPostDTO;
use App\DTO\WebzPostResponseDTO;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebzApiService
{
    private int $writeBatchSize;
    private string $token;
    private string $baseUrl;

    public function __construct(private readonly PostService $postService)
    {
        $this->baseUrl        = 'https://api.webz.io/newsApiLite';
        $this->token          = config('webz.api_token');
        $this->writeBatchSize = 200;
    }


    /**
     * @param array $queryParams
     * @param callable|null $callback
     *
     * @throws Throwable
     */
    public function fetchPosts(array $queryParams, ?callable $callback = null): void
    {
        $validQueryParams = $this->sanitizeQueryParams($queryParams);
        $builder          = new WebzQueryBuilder($this->baseUrl, $this->token);
        $url              = $builder->setQuery($validQueryParams['q'])
            ->setSort($validQueryParams['sort'])
            ->setOrder($validQueryParams['order'])
            ->setSentiment($validQueryParams['sentiment'])
            ->setHighlight($validQueryParams['highlight'])
            ->setSize($validQueryParams['size'])
            ->build();

        $totalPostsRetrieved = -1;
        $totalPostsAvailable = 0;
        $nextUrl             = $url;

        $writeBatch = [];

        try {
            DB::beginTransaction();

            $logMessage = "-------FETCH STARTED---------" . now()->toDateTimeString();
            logger()->info($logMessage);

            while ($this->shouldContinueFetchingMore($totalPostsAvailable, $totalPostsRetrieved)) {
                if ($totalPostsRetrieved === -1) {
                    $totalPostsRetrieved = 0;
                }

                $response = Http::get($nextUrl);

                if ($response->failed()) {
                    $this->handleFailCase($response);
                }

                $dtoResponse = new WebzPostResponseDTO($response->json());

                if (empty($dtoResponse->posts)) {
                    break;
                }

                $totalPostsAvailable = $dtoResponse->totalResults;


                foreach ($dtoResponse->posts as $post) {
                    if ($this->shouldWriteToDb($writeBatch)) {
                        $this->postService->writeToDb($writeBatch);

                        // TODO: Determine if this commit is appropriate. Batch-wise commit vs process commit.
                        DB::commit();

                        $logMessage = sprintf("Wrote %s posts to database.", count($writeBatch));
                        logger()->info($logMessage);

                        $writeBatch = [];
                    }

                    // TODO : Fix this. $totalPostsRetrieved and write count doesn't add up because i'm assigning UUID as array key, causing duplicate posts to be treated as one.
                    $postDto               = (array)new WebzPostDTO($post);
                    $postUUID              = $postDto['uuid'];
                    $writeBatch[$postUUID] = $postDto;

                    $totalPostsRetrieved++;
                }

                $nextUrl = $this->prepareNextUrl($dtoResponse);

                if ($callback) {
                    $remaining = $totalPostsAvailable - $totalPostsRetrieved;
                    $remaining = max($remaining, 0);

                    $callback($totalPostsRetrieved, $remaining);
                }

                sleep(1);
            }

            $logMessage = "-------FETCH COMPLETED---------" . now()->toDateTimeString();
            logger()->info($logMessage);
            //DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            logger()->error($exception->getMessage());
        }
    }

    /**
     * Making sure only valid query params are returned.
     *
     * @param array $queryParams
     *
     * @return array
     */
    private function sanitizeQueryParams(array $queryParams): array
    {
        return [
            'q'         => trim(Arr::get($queryParams, 'q', '')),
            'sort'      => trim(Arr::get($queryParams, 'sort', 'relevance')),
            'order'     => trim(Arr::get($queryParams, 'order', 'desc')),
            'sentiment' => trim(Arr::get($queryParams, 'sentiment', 'negative')),
            'highlight' => trim(Arr::get($queryParams, 'highlight', 'true')),
            'size'      => trim(Arr::get($queryParams, 'size', '10')),
        ];
    }

    /**
     * Checks if Total available post count greater or equal to Total posts retrieved.
     *
     * @param int $totalPostsAvailable
     * @param int $totalPostsRetrieved
     *
     * @return bool
     */
    private function shouldContinueFetchingMore(int $totalPostsAvailable, int $totalPostsRetrieved): bool
    {
        return $totalPostsAvailable > $totalPostsRetrieved;
    }

    /**
     * Throw exception on fail.
     *
     * @param $response
     *
     * @throws Exception
     */
    private function handleFailCase($response)
    {
        Log::error('Failed to fetch from Webz API', ['status' => $response->status(), 'body' => $response->body()]);

        throw new Exception('API request failed with status: ' . $response->status());
    }

    /**
     * Checks if the write batch size is greater than actual writeable batch size.
     *
     * @param array $writeBatch
     *
     * @return bool
     */
    private function shouldWriteToDb(array $writeBatch): bool
    {
        return count($writeBatch) >= $this->writeBatchSize;
    }

    /**
     * Prepare next url via string manipulation, using the response->next.
     *
     * @param WebzPostResponseDTO $dtoResponse
     *
     * @return string
     */
    private function prepareNextUrl(WebzPostResponseDTO $dtoResponse): string
    {
        $dtoNextUrl = str_replace('/newsApiLite', '', $dtoResponse->next);

        return $this->baseUrl . $dtoNextUrl;
    }
}