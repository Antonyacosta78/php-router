<?php

class Router{
    
    public function __construct(Collector $collector){
        $this->collector = $collector;
    }

    public function resolve($route){
        $params = $this->collector->match($route);
    }

}