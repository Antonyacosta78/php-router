<?php

class Collector{

    public $handlers = [];
    public $errorHandlers; 
    public $filters;
    private $classRoute;
    private $restrictedClasses = []; 

    public function __construct(){
        $this->classRoute = $this->parseRoute("/{classname: \w+}/{method: \w+}/?{params: .*?}");
    }

    public function error(int $error, $closure)
    {
        try{    
            if(is_callable($closure)){
                $this->errorHandlers[$error] = $closure;
                return true;
            }
            throw new Exception("Invalid parameter, expected callable function, ".gettype($closure)." given");
            return false;
        }catch(Exception $e){
            echo "Caught Exception [".$e->getMessage()."] when defining error handler for Errno {$error} please fix that to continue";
            die();            
        }
    }

    public function filter($name, $closure)
    {
        try{
            if(isset($this->filters[$name])){
                throw new Exception("Invalid parameter, filter {$name} already exists");
                return false;
            }    
            if(is_callable($closure)){
                $this->filters[$name] = $closure;
                return true;
            }
            throw new Exception("Invalid parameter, expected a callable, ".gettype($closure)." given");
            return false;
        }catch(Exception $e){
            echo "Caught Exception [".$e->getMessage()."] when defining filter {$name} please fix that to continue";
            die();
        }
            
    }

    public function get(string $route, $function, $options = [])
    {
        return $this->addRoute($route, "get", $function, $options);
    }

    public function post(string $route, $function, $options = [])
    {
        return $this->addRoute($route, "post", $function, $options);
    }

    public function any(string $route, $function, $options = [])
    {
       return $this->addRoute($route, "any", $function, $options);
    }

    public function checkMethod($index, string $method)
    {
        $method = strtolower($method);
        if($method !== "get" && $method !== "post"){//only get and post supported, sorry
            return false;
        }
        if($index === "classRoute"){return true;}
        if($this->handlers[$index]["method"] === "any"){
            return true;
        }
        return $method === $this->handlers[$index]["method"];
    }

    public function match($route)
    {
        $matches = [];
        $match = 0;
        $i = 0;
        while($match === 0 && $i < count($this->handlers)){
        $match = preg_match(
                $this->handlers[$i]["match"],
                $route,
                $matches
            );
        $i++;
        }

        if($match !== 0){//if match sucessful
            array_shift($matches);
            return [$i-1, $matches]; 
        }else{//failed match on defined routes
            $match = $this->matchOnClass($route);            
        }
        if($match === 0){//failed match on class
            return 404;
        }
        //match sucessful on class
        $match = preg_match(
            $this->classRoute,
            $route,
            $matches
        );
        $i = "classRoute";
        
        array_shift($matches);
        $matches[2] = isset($matches[2]) ? explode("/", $matches[2]) : null;
        return [$i, $matches];   
    }

    private function matchOnClass($route)
    {
        $route = explode("/",ltrim($route, "/")); //quebra em um array usando "/" como separador, se tem uma barra no começo, remove primeiro
        $classname = array_shift($route);
        $method = array_shift($route);
        $params = $route;

        if( !class_exists($classname) || (class_exists($classname) && !is_subclass_of($classname, "Controller", true)) ){
            return 0;
        }
        $class = new $classname();

        if( !method_exists($class, $method)){
            // throw new Exception("Invalid Route, class {$classname} exists but method is invalid"); 
            return 0; // por segurança
        }

        return true;
    }

    public function restrict(array $paths, array $filters)
    {
        try{
            extract($filters);
            if( isset($before) && !isset($this->filters[$before]) ){
                throw new Exception("Invalid parameter, filter {$before} does not exist");
            }
            if( isset($after) && !isset($this->filters[$after]) ){
                throw new Exception("Invalid parameter, filter {$after} does not exist");
            }

            $this->restrictedClasses = array_merge($this->restrictedClasses, array_fill_keys($paths, $filters));
        }catch(Exception $e){ 
            echo "Caught Exception [".$e->getMessage()."] while executing restrict, (classes to restrict: ";
            var_dump($paths);
            echo ") please fix this to continue"; 
            die();
        }
    }

    public function call($index, array $args = [])
    {
        $return = null;        
        if($index == "classRoute"){
            list($classname, $method, $params) = $args;
            $class = new $classname();
            
            if(isset($this->restrictedClasses[$classname])){
               extract($this->restrictedClasses[$classname]);

            }elseif(isset($this->restrictedClasses["$classname/$method"])){
                extract($this->restrictedClasses["$classname/$method"]);
            }
            //extract() takes indexes for naming new variables with the corresponding value

            if(isset($before)){
                $return = $this->filters[$before]();
            }
            if(is_null($return)){
                $class->$method(...$params);
                if(isset($after)){
                    $this->filters[$after]();
                }
            }
        }else{
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
                return $this->handlers[$index]["load"];
            }
        }
    //----
    }

    private function addRoute($route, $method, $closure, $options)
    {
        try{
            if( !is_callable($closure) ){
                throw new Exception("Invalid parameter, expected callable function, ".gettype($closure)." given");
                return false;
            }
            extract($options);
            if( isset($filters["before"]) && !isset($this->filters[$filters['before']]) ){
                throw new Exception("Invalid parameter: filter ".$filters['before']." (defined as filter before) does not exist");
                return false;
            }
            if( isset($filters["after"]) && !isset($this->filters[$filters['after']]) ){
                throw new Exception("Invalid parameter: filter ".$filters['after']." (defined as filter after) does not exist");
                return false;
            }
            $this->handlers[] = [
                    "method"=>$method,
                    "call"=>$closure,
                    "match"=>$this->parseRoute($route),
                    "before"=>isset($filters["before"]) ? $filters["before"] : NULL,
                    "after"=>isset($filters["after"]) ? $filters["after"] : NULL,
                    "load"=>isset($struct) ? $struct : NULL
                ];
                return true;
        }catch(Exception $e){
            echo "Caught Exception [".$e->getMessage()."] while defining route {$route} please fix this to continue"; 
            die();
        }
    }

    private function parseRoute($route)
    {
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