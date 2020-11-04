<?php

namespace App\Cache;

use Illuminate\Support\Facades\Redis;

class RedisAdapter implements CacheInterface
{

    public function get($key)
    {
        return Redis::get($key);
    }

    public function put($key, $value, $minutes = null)
    {
        if ($minutes === null)
            return $this->forever($key, $value);

        return Redis::setex($key, (int) max(1, $minutes * 60), $value);
    }

    public function forever($key, $value)
    {
        return Redis::set($key, $value);
    }

    public function remember($key, callable $callback, $minutes = null)
    {
        if (!is_null($value = $this->get($key)))
            return $value;

        $this->put($key, $value = $callback(), $minutes);
        return $value;
    }

    public function forget($key)
    {
        return Redis::del($key);
    }

    public function forgetByPattern($key_pattern)
    {
        if (count(Redis::keys($key_pattern)) > 0)
            return Redis::del(Redis::keys($key_pattern));
    }
}
