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
$example = "/queen/elizabeth/husband/property/age/something/1";


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
    return "/".preg_replace("/\//","\/",$pattern)."/";
}

echo "<br><br>route<br>";
var_dump($route);


echo "<br><br>formatRoute<br>";
$formatted = formatRoute($route);
var_dump($formatted);

preg_match($formatted,$example,$matches);

echo "<br><br>match example<br>";

var_dump($matches);
