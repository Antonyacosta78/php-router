<?php 
// echo "Hello World!".PHP_EOL;

// include_once "Collector.class.php";
// include_once "Router.class.php";

// $collector = new Collector;

// $collector->get("/",function(){
//     echo "It's Working";
// });

// $router = new Router($collector);
// $router->resolve("/");
$route = "/queen/{dat: \w*?}/husband/property/{propname}/something/{di}";

$replaced = preg_replace("/\//","\/",$route);

preg_match_all("/(\{.*?\})/",$route,$patternParams); //---
preg_match_all("/\{\w+:?(.*?)\}/",$route,$pttParams); //---

$patterns = array_map(function($e){
    return ($e === "") ? "(.*)" : "(".trim($e).")";
},$pttParams[1]);
$params = $patternParams[1]; //---

$patternReady = str_replace($params, $patterns, $route); //--

// echo "<br><br>route<br>";
// var_dump($route);
// echo "<br><br>replaced<br>";
// var_dump($replaced);



// echo "<br><br>patternParams<br>";
// var_dump($patternParams);

// echo "<br><br>pttParams<br>";
// var_dump($pttParams);

echo "<br><br>patterns<br>";
var_dump($patterns);

echo "<br><br>params<br>";
var_dump($params);

echo "<br><br>patternReady<br>";
var_dump($patternReady);



function formatRoute($route){
    preg_match_all("/(\{.*?\})/",$route,$keys);
    preg_match_all("/\{\w+:?(.*?)\}/",$route,$values);
    $pattern = str_replace(
                $keys[1], 
                array_map(function($e){
                    return ($e === "") ? "(.*)" : "(".trim($e).")";
                },$values[1]),
                $route
            );
    return preg_replace("/\//","\/",$pattern);
}

echo "<br><br>formatRoute<br>";
var_dump(formatRoute($route));
