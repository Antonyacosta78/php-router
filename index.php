<?php 
echo "Hello World!".PHP_EOL;

include_once "Collector.class.php";
include_once "Router.class.php";

$collector = new Collector;

$collector->get("/",function(){
    echo "It's Working";
});

$router = new Router($collector);
$router->resolve("/");