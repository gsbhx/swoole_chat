<?php


namespace app\services;

use pool\pool;
use services\UploadService;
use Swoole\Mysql\Exception;

/**
 * 消息类，所有的消息都在这里处理
 * Class MessageService
 * @package app\services
 */
class MessageService extends CommonService
{
    public function getMessageContent($params, $isComeIn = 0)
    {
        if($isComeIn==0){
            $msg_content="xxx进入了房间";
        }else{
            //TODO 消息敏感词过滤
            //TODO 消息入库
            $msg_content=$params['msg_content'];
        }
        return  [
            'first_topic' => $params['first_topic'],
            'second_topic' => $params['second_topic'],
            'msg_type' => 0,
            'msg_content' => $msg_content,
            'user_id' => $params['user_id'],
        ];
    }
}