<?php

namespace App\Cache;

use Illuminate\Support\Facades\Redis;

class RedisAdapter implements CacheInterface
{

    public function get($key)
    {
        if (env('REDIS_STATUS'))
            return Redis::get($key);
        return true;
    }

    public function put($key, $value, $minutes = null)
    {
        if (env('REDIS_STATUS')) {
            if ($minutes === null)
                return $this->forever($key, $value);

            return Redis::setex($key, (int) max(1, $minutes * 60), $value);
        }
        return $value;
    }

    public function forever($key, $value)
    {
        if (env('REDIS_STATUS'))
            return Redis::set($key, $value);
        return true;
    }

    public function remember($key, callable $callback, $minutes = null)
    {
        if (env('REDIS_STATUS')) {
            if (!is_null($value = $this->get($key)))
                return $value;

            $this->put($key, $value = $callback(), $minutes);
            return $value;
        }
        return $this->put($key, $value = $callback(), $minutes);
    }

    public function forget($key)
    {
        if (env('REDIS_STATUS'))
            return Redis::del($key);
        return true;
    }

    public function forgetByPattern($key_pattern)
    {
        if (env('REDIS_STATUS'))
            if (count(Redis::keys($key_pattern)) > 0)
                return Redis::del(Redis::keys($key_pattern));
        return true;
    }
}
