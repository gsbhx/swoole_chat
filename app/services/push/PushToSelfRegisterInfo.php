<?php
namespace app\services\push;

use app\services\CommonService;

class PushToSelfRegisterInfo extends PushCommonService implements PushObServer
{
    protected $server;
    protected $msg;
    protected $fds;
    public function __construct(\swoole_websocket_server $server,$msg, $fds)
    {
        $this->server=$server;
        $this->msg=$msg;
        $this->fds=$fds;
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        $this->result['msg'] = $this->msg;
        $params = json_encode($this->result, JSON_UNESCAPED_UNICODE);
        if(!$this->fds){
            return false;
        }
        foreach ($this->fds as $fd) {
            $this->server->push($fd, $params);
        }
        return true;
    }
}