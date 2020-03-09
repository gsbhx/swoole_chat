<?php


namespace app\services;

use pool\pool;

class LoginService extends CommonService
{


    public function __construct($server) {
        $this->server = $server;
        parent::__construct();
    }

    public function register($frame, $params) {
        try {
            //注册 一级和二级topic不能为空。
            if (!$params['first_topic'] || !$params['second_topic']) {
                throw new \Exception("当前用户的分组不存在，请重试！");
            }
            if (!$params['user_id']) {
                throw new \Exception("用户ID不存在，请重试！");
            }

            $result=$this->saveToRedis($frame,$params);
            //TODO 插入redis出错处理
            if(!$result){
                throw new \Exception('插入redis出错，请重试！');
            }
            $this->notify();
        } catch (\Exception $e) {
            $this->result['status'] = 1;
            $this->result['msg'] = $e->getMessage();
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }
        return $this->result;

    }

    /**
     * 用户断开后的操作
     * @param $fd int ws的fd标识
     */
    public function close($fd) {
        try{
            $result=$this->removeFromRedis($fd);
            if(!$result){
                throw new \Exception('删除用户失败，请重试！');
            }
            //在这里也加上notify,如果有需要通知的内容，可以在前面直接写，不用再更改此处的业务逻辑
            $this->notify();
        }catch (\Exception $e){
            $this->result['status'] = 1;
            $this->result['msg'] = $e->getMessage();
            echo json_encode($this->result,JSON_UNESCAPED_UNICODE);
        }
        return $this->result;
    }


    private function saveToRedis($frame,$params){
        //存入组-用户中
        $rk = str_replace(['first_topic', 'second_topic'], [$params['first_topic'], $params['second_topic']], self::redis_key_group_user);
        $user_list = [];
        if (pool::redis()->exists($rk)) {
            $user_list = explode(",", pool::redis()->get($rk));
        }
        $user_list[] = $params['user_id'];
        pool::redis()->set($rk, implode(",", array_unique(array_filter($user_list))));

        //存入 用户-组
        $rk = str_replace('fd', $frame->fd, self::redis_key_user_group);
        pool::redis()->set($rk, $params['first_topic'] . "_" . $params['second_topic']);
        //用户对应的fd
        pool::redis()->hSet(self::user_bind_redis_key, $frame->fd, $params['user_id']);
        //fd对应的user
        pool::redis()->hSet(self::fd_bind_user_redis_key, $params['user_id'], $frame->fd);
        return true;
    }

    private function removeFromRedis($fd){
        $user_id = pool::redis()->hGet(self::user_bind_redis_key, $fd);
        pool::redis()->hDel(self::user_bind_redis_key, $fd);
        //删除当前用户所对应的分组
        $rk = str_replace('fd', $fd, self::redis_key_user_group);
        $group = pool::redis()->get($rk);
        pool::redis()->del($rk);
        //删除分组中对应的用户
        if ($group) {
            $group = explode("_", $group);
            //从分组对应的用户中，删除当前用户
            $rk = str_replace(['first_topic', 'second_topic'], [$group[0], $group[1]], self::redis_key_group_user);
            $userlist = pool::redis()->get($rk);
            $userlist = explode(',', $userlist);
            $key = array_search($user_id, $userlist);
            array_splice($userlist, $key, 1);
            if (!$userlist) {
                pool::redis()->del($rk);
            } else {
                pool::redis()->set($rk, implode(",", $userlist));
            }
        }
        //删除fd绑定的用户。
        pool::redis()->hDel(self::fd_bind_user_redis_key, $user_id);
        return true;
    }


}