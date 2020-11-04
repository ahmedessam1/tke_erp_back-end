<?php

namespace App\Cache;

interface CacheInterface
{
    // GET DATA BY KEY
    public function get($key);

    // PUTTING DATA TO CACHE AND HAVE THE OPTION TO ADD TIME
    public function put($key, $value, $minutes = null);

    // PUTTING DATA TO CACHE WITHOUT EXPIRY DATE
    public function forever($key, $value);

    // PUT DATA FOR A CERTAIN AMOUNT OF TIME 'IF' IT DOES NOT EXISTS
    public function remember($key, callable $callback, $minutes = null);

    // REMOVE FROM CACHE
    public function forget($key);
}