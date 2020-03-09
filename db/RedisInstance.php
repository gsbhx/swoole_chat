<?php

namespace db;
class RedisInstance
{
    protected static $db;
    private function __construct()
    {

    }

    public static function getInstance(){
        if(!self::$db){
            //获取config配置文件
            $params=PARAMS[CHATENV];
            self::$db = new \Redis();
            self::$db->connect('127.0.0.1', 6379);
        }
        return self::$db;
    }
}