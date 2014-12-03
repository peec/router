<?php

require "../vendor/autoload.php";

use \Pkj\Router\Router,
    \Pkj\Router\Route;




$router = new Router(\Pkj\Router\Request::bindFromHttpFactory());


$router->addRoute(new Route('home', Route::GET | Route::POST, "^/$"), function () use($router) {


    echo "Hello World.<br />
    <a href='".$router->get('uia')->reverse(['test'=>'hei'])."'>uia route</a><br />
    <a href='".$router->get('what')->reverse(['is'=>'123', 'saying'=>'321'])."'>what route</a>";
});


$router->addRoute(new Route('uia', Route::GET, "^/uia/:<.*?>test$"), function ($arg1) use ($router) {
    echo "Hello $arg1. <a href='".$router->get('home')->reverse()."'>home route</a>";
});


$router->addRoute(new Route('what', Route::GET, '^/what/:<\d+>is/you/:saying$'), function ($arg1, $arg2) use ($router) {
    echo "Hello $arg1,$arg2. <a href='".$router->get('home')->reverse()."'>home route</a>";
});


if ($executable = $router->run()) {
    $executable->execute();
} else {
    http_send_status(404);
    echo "<h1>404</h1>";
}


