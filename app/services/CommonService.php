<?php


namespace app\services;

use app\services\push\PushEventGenerator;
use pool\pool;
use services\LogService;

/**
 * Class CommonService
 * @package app\services
 */
class CommonService extends PushEventGenerator
{
    const  redis_key_group_user = 'ws_topic_first_topic_second_topic';//分组下对应的用户
    const  redis_key_user_group = 'ws_user_fd';//用户对应的分组
    const  user_bind_redis_key = "ws_user_bind_fd_redis_key";//fd绑定用户的redis_key fd=》user
    const  fd_bind_user_redis_key = "ws_fd_bind_user_redis_key";//用户绑定fd的redis_key user=>fd
    const  redis_expire_time=86400;


    public  $result = ['status' => 0, 'msg' => 'success', 'data' => []];
    public  $server;
    public function __construct() {

    }

}