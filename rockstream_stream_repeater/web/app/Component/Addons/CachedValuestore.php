<?php

namespace App\Component\Addons;

use Spatie\Valuestore\Valuestore;

/**
 * Modified Implements a cache for the values stored.
 * 
 * Source Code: https://github.com/timacdonald/cached-valuestore
 */

class CachedValuestore extends Valuestore
{
    protected $cache;

    /**
     * Get all values from the store.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->cache ?? $this->cache = parent::all();
    }

    /**
     * Get a value from the store.
     * 
     * @param string $key
     * @param mixed $default
     */
    public function get($key, $default = null): mixed
    {
        return $this->cache[$key] ?? $this->cache[$key] = parent::get($key, $default);
    }

    /**
     * Set the valuestore contents.
     *
     * @param  array $values
     * @return $this
     */
    protected function setContent(array $values): static
    {
        return parent::setContent($values);
    }

    /**
     * Clears the local cache.
     *
     * @return $this
     */
    public function clearCache()
    {
        $this->cache = null;

        return $this;
    }
}
