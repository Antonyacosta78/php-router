<?php

class Collector{

    public $handlers;
    public $errorHandlers; 
    public $filters;

    public function error(int $error, $closure){
        if(is_callable($closure)){
            $this->errorHandlers[$error] = $closure;
            return true;
        }
        return false;
    }

    public function filter($name, $closure){
        if(is_callable($closure)){
            $this->filters[$name] = $closure;
            return true;
        }else{
            throw new Exception("Invalid parameter, expected a callable, ".gettype($closure)." given");
        }
        return false;
    }

    public function get(string $route, $function, $filters = []){
        return $this->addRoute($route, "get", $function, $filters);
    }

    public function post(string $route, $function, $filters = []){
        return $this->addRoute($route, "post", $function, $filters);
    }

    public function any(string $route, $function, $filters = []){
       return $this->addRoute($route, "any", $function, $filters);
    }

    public function checkMethod(int $index, string $method){
        $method = strtolower($method);
        if($method !== "get" && $method !== "post"){//only get and post supported, sorry
            return false;
        }
        if($this->handlers[$index]["method"] === "any"){
            return true;
        }
        return $method === $this->handlers[$index]["method"];
    }

    public function call(int $index, array $args = []){
        if($this->handlers[$index]["before"] !== null){
            $return = $this->filters[$this->handlers[$index]["before"]]();
        }
        if(is_null($return)){   
            if($args === []){
                $this->handlers[$index]["call"]();
            }else{
                $this->handlers[$index]["call"](...$args);
            }
            if($this->handlers[$index]["after"] !== null){
                $this->filters[$this->handlers[$index]["after"]]();
            }    
        }
    }

    private function addRoute($route, $method, $closure, $filters){
        try{
            if( !is_callable($closure) ){
                throw new Exception("Invalid parameter, expected callable function, ".gettype($closure)." given");
                return false;
            }
            if( !isset($filter["before"]) && !isset($this->filters[$filter['before']]) ){
                throw new Exception("Invalid parameter: filter {$filter['before']} does not exist");
                return false;
            }
            if( !isset($filter["after"]) && !isset($this->filters[$filter['after']]) ){
                throw new Exception("Invalid parameter: filter {$filter['after']} does not exist");
                return false;
            }
            $this->handlers[] = [
                    "method"=>$method,
                    "call"=>$closure,
                    "match"=>$this->parseRoute($route),
                    "before"=>$filters["before"],
                    "after"=>$filters["after"]
                ];
                return true;
        }catch(Exception $e){
            echo "Caught Exception [".$e->getMessage()."] while defining route {$route} please fix this to continue"; 
            die();
        }
    }

    private function parseRoute($route){
        preg_match_all("/(\{.*?\})/",$route,$keys);
        preg_match_all("/\{\w+:?(.*?)\}/",$route,$values);
        $pattern = str_replace(
                    $keys[1], 
                    array_map(function($e){
                        return ($e === "") ? "(.*)" : "(".trim($e).")";
                    },$values[1]),
                    $route
                );
        return "/^".preg_replace("/\//","\/",$pattern)."$/";
    }
   

}