<?php

declare(strict_types = 1);

namespace App\DTO;

use Exception;

class WebzPostResponseDTO
{

    public array $posts;
    public int $totalResults;
    public int $moreResultsAvailable;
    public string $next;
    public int $requestsLeft;
    public mixed $warnings;

    /**
     * @throws Exception
     */
    public function __construct(array $response)
    {
        if (!$response) {
            throw new Exception('Something went wrong');
        }

        $this->posts                = $response['posts'];
        $this->totalResults         = $response['totalResults'];
        $this->moreResultsAvailable = $response['moreResultsAvailable'];
        $this->next                 = $response['next'];
        $this->requestsLeft         = $response['requestsLeft'];
        $this->warnings             = $response['warnings'];
    }
}