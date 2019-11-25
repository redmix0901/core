<?php

namespace Core\Services\Cache;

interface CacheInterface
{

    public function get($key);

    public function put($key, $value, $minutes = false);

    public function has($key);

    public function flush();
}
