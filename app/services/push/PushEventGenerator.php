<?php


namespace app\services\push;

abstract class PushEventGenerator
{
    protected $events=[];
    public function addPushObServer(PushObServer $obServer){
        $this->events[]=$obServer;
    }

    public function notify(){
        foreach($this->events as $event){
            $event->update();
        }
    }

}