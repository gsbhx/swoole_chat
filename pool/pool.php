<?php

namespace pool;
use db\RedisInstance;

class pool{

    public function __construct() {
    }

    public static function redis(){
        return RedisInstance::getInstance();
    }
}