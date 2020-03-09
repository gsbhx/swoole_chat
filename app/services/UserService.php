<?php


namespace app\services;


use pool\pool;

class UserService extends CommonService
{
    /**
     * 根据分组获取需要推送的fd
     * @param $first_topic
     * @param $second_topic
     * @return array
     */
    public static function getFdByGroup($first_topic, $second_topic) {
        //获取group下的fd
        $rk = str_replace(['first_topic', 'second_topic'], [$first_topic, $second_topic], self::redis_key_group_user);
        $result = explode(",",pool::redis()->get($rk)) ?: [];
        var_dump($result);
        $fds=[];
        foreach($result as $k => $v){
            $fds[]=pool::redis()->hGet(self::fd_bind_user_redis_key,$v);
        }
        echo "getFdByGroup================".json_encode($fds). "\n";
        return array_filter($fds);
    }
}