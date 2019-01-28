<?php

class Router{

    private $collector;
    private $requestMethod;
    private $prefix;
    
    public function __construct(Collector $collector, string $prefix = "")
    {
        $this->collector = $collector;
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->prefix  = strlen($prefix);
    }

    public function resolve($route)
    {
        try{
            if($this->prefix !== 0){
                $route = substr_replace($route, "", 0, $this->prefix);
            }
            $data = $this->collector->match($route);
            if($data === 404){
                $this->collector->errorHandlers[404]();
                return false;
            }
            if(!$this->collector->checkMethod($data[0], $this->requestMethod)){
                
                $this->collector->errorHandlers[405]();
                return false;
            }
            $this->collector->call(...$data);
            
            return true;
        }catch(Exception $e){
            echo "Caught Exception [".$e->getMessage()."] while resolving route {$route} please fix this to continue"; 
        }
    }

}