<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'uuid',
        'ord_in_thread',
        'url',
        'parent_url',
        'author',
        'title',
        'language',
        'sentiment',
        'text',
        'highlight_text',
        'highlight_title',
        'highlight_thread_title',
        'rating',
        'thread',
        'social',
        'categories',
        'topics',
        'external_links',
        'external_images',
        'trust',
        'syndication',
        'ai_allow',
        'has_canonical',
        'webz_reporter',
        'breaking',
        'published',
        'crawled',
        'updated'
    ];

    protected $casts = [
        'rating'          => 'float',
        'thread'          => 'json',
        'social'          => 'json',
        'categories'      => 'json',
        'topics'          => 'json',
        'external_links'  => 'json',
        'external_images' => 'json',
        'trust'           => 'json',
        'syndication'     => 'json',
        'ai_allow'        => 'boolean',
        'has_canonical'   => 'boolean',
        'webz_reporter'   => 'boolean',
        'breaking'        => 'boolean',
    ];
}
