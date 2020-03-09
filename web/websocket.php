<?php

namespace websockets;

use Swoole;
use app\controllers\MessageController;
use swoole_websocket_server;

class websocket
{

    private $server;
    private $MessageController;

    public function __construct()
    {
    }

    public function run()
    {
        //开启swoole
        $this->server = new Swoole\WebSocket\Server("0.0.0.0", 9501);

        $this->MessageController = new MessageController($this->server);

        $this->server->on('open', function (swoole_websocket_server $server, $request) {
            echo "server: handshake success with fd{$request->fd}\n";
        });


        $this->server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
            $this->MessageController->getMessage($frame);
        });


        $this->server->on('close', function ($ser, $fd) {
            echo "server: close a fd  : fd{$fd}\n";
            $this->MessageController->closeFd($fd);
        });

        $this->server->start();
    }
}
