<?php 
//test file

echo "Hello World!".PHP_EOL;

include_once "Collector.class.php";
include_once "Router.class.php";

$collector = new Collector();

$collector->error(404, function(){
    echo "sorry pal, no matches";
});
$collector->error(405, function(){
    echo "hey, you should not be using ".$_SERVER["REQUEST_METHOD"];
});

$collector->filter("testbefore", function(){
    echo "I'm executed before the route. ";
});

$collector->filter("testafter", function(){
    echo "I'm executed after the example route. ";
});

$collector->get("/",function(){
    echo "It's Working";
});

$collector->get("/example", function(){
    echo " I am the example Route. ";
}, ["filters"=>['before'=>"testbefore", 'after'=>"testafter"]]);

$collector->get("/realroute/{id}/{sec}", function($id, $sec){
    echo "Params are working! got $id and $sec . ";
});

$collector->filter("restrictHOR", function(){
    echo "lol nope"; return 1;
});



$router = new Router($collector, "/router");

$router->resolve("/router/example");



// $collector->restrict(["Class1", "class2/res", "class3/meth"], ["before"=>"auth", "after"=>"log"]);