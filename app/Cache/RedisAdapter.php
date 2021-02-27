<?php

namespace App\Cache;

use Illuminate\Support\Facades\Redis;
use Auth;
use DB;

class RedisAdapter implements CacheInterface
{
    private function userTenant()
    {
        $tenant_id = Auth::user()->tenant_id;
        $tenant = DB::connection('landlord')->table('tenants')->find($tenant_id);
        return $tenant->name.':';
    }

    public function get($key)
    {
        $key = $this->userTenant().$key;
        if (env('REDIS_STATUS'))
            return Redis::get($key);
        return true;
    }

    public function put($key, $value, $minutes = null)
    {
        $key = $this->userTenant().$key;
        if (env('REDIS_STATUS')) {
            if ($minutes === null)
                return $this->forever($key, $value);

            return Redis::setex($key, (int) max(1, $minutes * 60), $value);
        }
        return $value;
    }

    public function forever($key, $value)
    {
        $key = $this->userTenant().$key;
        if (env('REDIS_STATUS'))
            return Redis::set($key, $value);
        return true;
    }

    public function remember($key, callable $callback, $minutes = null)
    {
        $key = $this->userTenant().$key;
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
        $key = $this->userTenant().$key;
        if (env('REDIS_STATUS'))
            return Redis::del($key);
        return true;
    }

    public function forgetByPattern($key_pattern)
    {
        $key_pattern = $this->userTenant().$key_pattern;
        if (env('REDIS_STATUS'))
            if (count(Redis::keys($key_pattern)) > 0)
                return Redis::del(Redis::keys($key_pattern));
        return true;
    }
}
