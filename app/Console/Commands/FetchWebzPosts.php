<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\WebzApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchWebzPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-webz-posts {--q= : Required search query} {--params= : Optional query parameters as JSON or query string}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch posts from Webz API using CLI query params.';

    public function __construct(private readonly WebzApiService $webzApiService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $q = $this->option('q');

        if (!$q) {
            $this->error('The --q flag is required.');
            return;
        }

        $params = $this->option('params');
        $queryParams = ['q' => $q];

        if ($params) {
            if (str_starts_with($params, '{')) {
                $extraParams = json_decode($params, true);
            } else {
                parse_str($params, $extraParams);
            }

            if (!is_array($extraParams)) {
                $this->error('Invalid format for --params. Use JSON or query string.');
                return;
            }

            $queryParams = array_merge($queryParams, $extraParams);
        }

        $this->webzApiService->fetchPosts(
            queryParams: $queryParams,
            callback: function (int $retrieved, int $remaining) {

                $logMessage = "Progress: {$retrieved} fetched, {$remaining} remaining";
                logger()->info($logMessage);
            }
        );
    }
}
