<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\WebzPostDTO;
use App\Models\Posts;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class PostService
{
    /**
     * @param array<WebzPostDTO> $writeBatch
     *
     * @return void
     * @throws Throwable
     */
    public function writeToDb(array $writeBatch): void
    {
        try {
            Posts::upsert($writeBatch, ['uuid']);
        } catch (Throwable $e) {
            $collection = collect($writeBatch)->pluck('uuid')->all();

            $printable = [];
            foreach ($writeBatch as $item) {
                $printable[] = ["uuid" => $item['uuid'], "title" => $item['title']];
            }

            dd($collection, $printable, $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param array $filters
     * @param int|string $perPage
     *
     * @return LengthAwarePaginator
     */
    public function getPaginatedPosts(array $filters, int|string $perPage = 15): LengthAwarePaginator
    {
        $query = Posts::query();

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * @param $query
     * @param array $filters
     *
     * @return void
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['q'])) {
            $query->whereLike('title', $filters['q']);
        }

        if (!empty($filters['author'])) {
            $query->whereLike('author', $filters['author']);
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (!empty($filters['sentiment'])) {
            $query->where('sentiment', $filters['sentiment']);
        }

        if (isset($filters['ai_allow'])) {
            $query->where('ai_allow', filter_var($filters['ai_allow'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['webz_reporter'])) {
            $query->where('webz_reporter', filter_var($filters['webz_reporter'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['published'])) {
            $query->whereDate('published', $filters['published']);
        }

        if (!empty($filters['crawled'])) {
            $query->whereDate('crawled', $filters['crawled']);
        }
    }
}