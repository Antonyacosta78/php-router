<?php

class Collector{

    private $handlers;

    public function get(string $route, $function){
        if(is_callable($function)){
            $this->handlers[$route] = [
                "type"=>"get",
                "call"=>$function
            ];
        }
    }

    public function post(string $route, $function){
        if(is_callable($function)){
            $this->handlers[$route] = [
                "type"=>"get",
                "call"=>$function
            ];
        }
    }

    public function any(string $route, $function){
        if(is_callable($function)){
            $this->handlers[$route] = [
                "type"=>"any",
                "call"=>$function
            ];
        }
    }

    public function call($route){
        $this->handlers[$route]["call"]();
    }

}