<?php

class Router{

    private $collector;
    private $requestMethod;
    private $prefix;
    
    public function __construct(Collector $collector, string $prefix = ""){
        $this->collector = $collector;
        $this->requestMethod = $_SERVER["REQUEST_METHOD"];
        $this->prefix  = strlen($prefix);
    }

    public function resolve($route){
        try{
            if($this->prefix !== 0){
                $route = substr_replace($route, "", 0, $this->prefix);
            }
            $data = $this->match($route);
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

    private function match($route){
        $matches = [];
        $match = 0;
        $i = 0;
        do{
        $match = preg_match(
                $this->collector->handlers[$i]["match"],
                $route,
                $matches
            );
        $i++;
        }while($match === 0 && $i < count($this->collector->handlers));
        if($match === 0){
            return $this->matchOnClass($route);
        }
        array_shift($matches);
        return [$i-1, $matches];   
    }

    private function matchOnClass($route){
        $route = explode("/",ltrim($route, "/"));
        $classname = array_shift($route);
        $method = array_shift($route);
        $params = $route;

        if(!class_exists($classname) || (class_exists($classname) && is_subclass_of($classname, "Controller", true))){
            return 404;
        }


        $class = new $classname();
        if(!method_exists($class, $method)){
            throw new Exception("Invalid Route: Matched on class found, but method was not found");
        }
    }
}