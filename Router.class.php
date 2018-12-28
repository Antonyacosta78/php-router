<?php

class Router{
    
    public function __construct(Collector $collector){
        $this->collector = $collector;
    }

    public function resolve($route){
        $this->collector->call($route);
    }

}