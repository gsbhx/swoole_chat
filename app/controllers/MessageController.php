<?php

namespace app\controllers;


use app\services\LoginService;
use app\services\MessageService;
use app\services\push\PushToAllMessage;
use app\services\push\PushToSelfRegisterInfo;
use app\services\UserService;

class MessageController extends CommonController
{
    protected $server;

    public function __construct(\swoole_websocket_server $server)
    {
        $this->server = $server;
    }

    /**
     * 获取消息，并按照实际情况去分发
     * @param $frame \swoole_websocket_frame
     */
    public function getMessage(\swoole_websocket_frame $frame)
    {
        $data = json_decode($frame->data, true);
        switch ($data['type']) {
            case 'login':
                $loginService = new LoginService($this->server);
                //注入推送给自己注册成功的方法；
                $pushtoselfstatus = new PushToSelfRegisterInfo($this->server, '注册成功', [$frame->fd]);
                $loginService->addPushObServer($pushtoselfstatus);
                //注入推送给所有人 “xxx进入房间”的消息。
                //拼装消息体
                $msgService=new MessageService();
                $data=$msgService->getMessageContent($data,0);
                $fds = UserService::getFdByGroup($data['first_topic'], $data['second_topic']);
                $pushToAll = new PushToAllMessage($this->server,$data,$fds);
                $loginService->addPushObServer($pushToAll);
                $result = $loginService->register($frame, $data);
                if($result['status']!=0){
                    echo json_encode($result,JSON_UNESCAPED_UNICODE) ."\n";
                }
                break;
            case 'message':
                $msgService=new MessageService();
                $data=$msgService->getMessageContent($data,1);
                $fds = UserService::getFdByGroup($data['first_topic'], $data['second_topic']);
                $pushToAll=new PushToAllMessage($this->server,$data,$fds);
                $msgService->addPushObServer($pushToAll);
                $msgService->notify();
                break;
        }
    }

    //关闭连接
    public function closeFd($fd)
    {
        $loginService = new LoginService($this->server);
        $loginService->close($fd);
    }

}