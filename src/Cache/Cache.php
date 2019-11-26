<?php

namespace Redmix0901\Core\Cache;

use File;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Arr;

class Cache implements CacheInterface
{
    /**
     * @var CacheManager
     */
    protected $cache;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    public $group;

    /**
     * Cache constructor.
     * @param \Illuminate\Cache\Repository|CacheManager $cache
     */
    public function __construct(CacheManager $cache, $group, $config = [])
    {
        $this->cache = $cache;
        $this->group = $group;
        $this->config = !empty($config) ? $config : [
            'cache_time'  => 10,
            'stored_keys' => storage_path('cache_keys.json'),
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public function generateCacheKey($key)
    {
        return md5($this->group) . $key;
    }

    /**
     * Retrieve data from cache.
     *
     * @param string $key Cache item key
     * @return mixed
     */
    public function get($key)
    {
        if (!file_exists($this->config['stored_keys'])) {
            return null;
        }
        return $this->cache->get($this->generateCacheKey($key));
    }

    /**
     * Add data to the cache.
     */
    public function put($key, $value, $minutes = false)
    {
        if (!$minutes) {
            $minutes = $this->config['cache_time'];
        }

        $key = $this->generateCacheKey($key);

        $this->storeCacheKey($key);

        $this->cache->put($key, $value, $minutes);

        return true;
    }

    /**
     * Test if item exists in cache
     * Only returns true if exists && is not expired.
     */
    public function has($key)
    {
        if (!file_exists($this->config['stored_keys'])) {
            return false;
        }
        $key = $this->generateCacheKey($key);

        return $this->cache->has($key);
    }

    /**
     * Store cache key to file
     *
     */
    public function storeCacheKey($key)
    {
        if (file_exists($this->config['stored_keys'])) {
            $cacheKeys = $this->openFile($this->config['stored_keys']);
            if (!empty($cacheKeys) && !in_array($key, Arr::get($cacheKeys, $this->group, []))) {
                $cacheKeys[$this->group][] = $key;
            }
        } else {
            $cacheKeys = [];
            $cacheKeys[$this->group][] = $key;
        }
        $this->saveFile($this->config['stored_keys'], $cacheKeys);
    }

    /**
     * Clear cache of an object
     *
     */
    public function flush()
    {
        $cacheKeys = [];
        if (file_exists($this->config['stored_keys'])) {
            $cacheKeys = $this->openFile($this->config['stored_keys']);
        }
        if (!empty($cacheKeys)) {
            $caches = Arr::get($cacheKeys, $this->group);
            if ($caches) {
                foreach ($caches as $cache) {
                    $this->cache->forget($cache);
                }
                unset($cacheKeys[$this->group]);
            }
        }
        if (!empty($cacheKeys)) {
            $this->saveFile($this->config['stored_keys'], $cacheKeys);
        } else {
            File::delete($this->config['stored_keys']);
        }
    }

    protected function saveFile($path, $data, $json = true)
    {
        try {
            if ($json) {
                $data = json_encode_prettify($data);
            }
            if (!File::isDirectory(dirname($path))) {
                File::makeDirectory(dirname($path), 493, true);
            }
            File::put($path, $data);

            return true;
        } catch (Exception $ex) {
            info($ex->getMessage());
            return false;
        }
    }

    protected function openFile($file, $convert_to_array = true)
    {
        $file = File::get($file);
        if (!empty($file)) {
            if ($convert_to_array) {
                return json_decode($file, true);
            } else {
                return $file;
            }
        }
        if (!$convert_to_array) {
            return null;
        }
        return [];
    }
}
