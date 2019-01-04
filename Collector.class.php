<?php

class Collector{

    public $handlers;

    public function get(string $route, $function){
        $this->addRoute($route, "get", $function);
    }

    public function post(string $route, $function){
        $this->addRoute($route, "post", $function);
    }

    public function any(string $route, $function){
       $this->addRoute($route, "any", $function);
    }

    public function checkMethod(int $index, string $method){
        return strtolower($method) === $this->handlers[$index]["method"];
    }

    public function call(int $index, array $args = []){
        if($args === []){
            $this->handlers[$index]["call"]();
        }else{
            $this->handlers[$index]["call"](...$args);
        }
        
    }

    private function addRoute($route, $method, $closure){
        if(is_callable($closure)){
            $this->handlers[] = [
                "method"=>$method,
                "call"=>$closure
            ];
            return true;
        }
        return false;
    }

    private function parseRoute($route){

    }
   

}