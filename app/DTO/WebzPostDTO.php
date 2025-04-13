<?php

declare(strict_types = 1);

namespace App\DTO;

use Illuminate\Support\Arr;

class WebzPostDTO
{
    public ?string $uuid;
    public ?string $url;
    public ?int $ord_in_thread;
    public ?string $parent_url;
    public ?string $author;
    public ?string $title;

    public ?string $text;
    public ?string $language;
    public ?string $external_links;
    public ?float $rating;
    public ?string $entities;
    public ?string $sentiment;
    public ?string $categories;
    public ?string $topics;
    public ?string $highlight_text;
    public ?string $highlight_thread_title;
    public ?string $highlight_title;

    public ?string $published;

    public ?string $crawled;
    public ?string $updated;

    public bool $webz_reporter;
    public bool $ai_allow;
    public bool $has_canonical;
    public bool $breaking;

    public ?string $thread;
    public ?string $social;
    public ?string $external_images;
    public ?string $trust;
    public ?string $syndication;

    /**
     * PostDTO constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->uuid                   = Arr::get($data, 'uuid');
        $this->ord_in_thread          = Arr::get($data, 'ord_in_thread');
        $this->url                    = Arr::get($data, 'url');
        $this->parent_url             = Arr::get($data, 'parent_url');
        $this->author                 = Arr::get($data, 'author');
        $this->title                  = Arr::get($data, 'title');
        $this->language               = Arr::get($data, 'language');
        $this->sentiment              = Arr::get($data, 'sentiment');
        $this->text                   = Arr::get($data, 'text');
        $this->highlight_text         = Arr::get($data, 'highlightText');
        $this->highlight_title        = Arr::get($data, 'highlightTitle');
        $this->highlight_thread_title = Arr::get($data, 'highlightThreadTitle');
        $this->rating                 = isset($data['rating']) ? (float)$data['rating'] : null;
        $this->thread                 = isset($data['thread']) ? json_encode($data['thread']) : null;
        $this->social                 = isset($data['social']) ? json_encode($data['social']) : null;
        $this->categories             = isset($data['categories']) ? json_encode($data['categories']) : null;
        $this->topics                 = isset($data['topics']) ? json_encode($data['topics']) : null;
        $this->external_links         = isset($data['external_links']) ? json_encode($data['external_links']) : null;
        $this->external_images        = isset($data['external_images']) ? json_encode($data['external_images']) : null;
        $this->trust                  = isset($data['trust']) ? json_encode($data['trust']) : null;
        $this->syndication            = isset($data['syndication']) ? json_encode($data['syndication']) : null;
        $this->ai_allow               = $data['ai_allow'] ?? false;
        $this->has_canonical          = $data['has_canonical'] ?? false;
        $this->webz_reporter          = $data['webz_reporter'] ?? false;
        $this->breaking               = $data['breaking'] ?? false;
        $this->published              = $data['published'] ?? null;
        $this->crawled                = $data['crawled'] ?? null;
        $this->updated                = $data['updated'] ?? null;
    }


}
