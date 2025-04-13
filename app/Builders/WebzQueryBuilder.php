<?php

declare(strict_types=1);

namespace App\Builders;

class WebzQueryBuilder
{
    protected string $baseUrl;
    protected string $token;
    protected array $params = [];

    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl         = $baseUrl;
        $this->token           = $token;
        $this->params['token'] = $token;
    }

    /**
     * Set the search query (e.g., 'Bitcoin', 'AI')
     */
    public function setQuery(string $query): self
    {

        if ($query) {
            $this->params['q'] = $query;
        }

        return $this;
    }

    /**
     * Set sort order (e.g., 'crawled', 'published')
     */
    public function setSort(string $sortBy): self
    {
        if ($sortBy) {
            $this->params['sort'] = $sortBy;
        }

        return $this;
    }

    /**
     * Set sort order (e.g., 'crawled', 'published')
     */
    public function setOrder(string $orderBy): self
    {
        if ($orderBy) {
            $this->params['order'] = $orderBy;
        }

        return $this;
    }

    /**
     * Set sort order (e.g., 'crawled', 'published')
     */
    public function setFrom(string $from): self
    {
        if ($from) {
            $this->params['from'] = $from;
        }

        return $this;
    }

    /**
     * Set sort order (e.g., 'crawled', 'published')
     */
    public function setSentiment(string $sentiment): self
    {
        if ($sentiment) {
            $this->params['sentiment'] = $sentiment;
        }

        return $this;
    }

    /**
     * Set sort order (e.g., 'crawled', 'published')
     */
    public function setHighlight(string $highlight): self
    {
        if ($highlight) {
            $this->params['highlight'] = $highlight;
        }

        return $this;
    }

    /**
     * Set the number of results per page (default max = 10)
     */
    public function setSize(string $size): self
    {
        if ($size) {
            $this->params['size'] = $size;
        }

        return $this;
    }

    /**
     * Build the final query URL
     */
    public function build(): string
    {
        return $this->baseUrl . '?' . http_build_query($this->params);
    }
}
