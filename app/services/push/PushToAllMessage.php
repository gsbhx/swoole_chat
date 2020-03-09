<?php
namespace app\services\push;


class PushToAllMessage extends PushCommonService implements PushObServer
{
    private $server;
    private $msg;
    private $fds;
    public function __construct(\swoole_websocket_server $server,$data, $fds)
    {
        $this->server=$server;
        $this->result['data']=$data;
        $this->fds=$fds;
    }

    /**
     * @inheritDoc
     */
    function update()
    {
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