<?php 
//example file
error_reporting(E_ALL);
echo "Hello World!".PHP_EOL;

include_once "Collector.class.php";

include_once "Router.class.php";

$collector = new Collector;

$collector->error(404, function(){
    echo "sorry pal, no matches";
});

$collector->error(405, function(){
    echo "hey, you should not be using {$_SERVER['REQUEST_METHOD']}";
});

$collector->get("/",function(){
    echo "It's Working";
});

$collector->any("/example", function(){
    echo "any is working!";
});

$collector->get("/realroute/{id}/{sec}", function($id, $sec){
    echo "Params are working! got $id and $sec";
});

$router = new Router($collector, "/router");

$router->resolve("/router/realroute/2/1");



// $route = "/nothing/else/matters/1";
// $example = "/nothing/else/matters/1";
// $prefix = "/else";
// echo "<pre>";
// var_dump($_SERVER);
// echo "</pre>";

// function resolve($route, $prefix = ""){
//     $prefix = strlen($prefix);
//     if($prefix !== 0){
//         $route = substr_replace($route, "", 0, $prefix);
//     }
//     return $route;

// }

// function formatRoute($route){
//     preg_match_all("/(\{.*?\})/",$route,$keys);
//     preg_match_all("/\{\w+:?(.*?)\}/",$route,$values);
//     $pattern = str_replace(
//                 $keys[1], 
//                 array_map(function($e){
//                     return ($e === "") ? "(.*)" : "(".trim($e).")";
//                 },$values[1]),
//                 $route
//             );
//     return "/^".preg_replace("/\//","\/",$pattern)."$/";
// }

// echo "<br><br>route<br>";
// var_dump($route);


// echo "<br><br>formatRoute<br>";
// $formatted = formatRoute($route);
// var_dump($formatted);

// $match = preg_match($formatted,$example,$matches);
// //array_shift($matches);

// echo "<br><br>match example<br>";

// var_dump($matches);
// echo "<br><br>resolve<br>";

// var_dump(resolve($route, $prefix));
