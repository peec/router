[![Build Status](https://travis-ci.org/pkj/router.png?branch=master)](http://travis-ci.org/pkj/router)


# PHP Router Component

Allows your project to integration a flexible `Router` component.


## Features

- Can be plugged into any existing code.
- Programmatic route definitions
- Configuration based routes from file
- Any callable can be assigned to a route
- Routes with named or un-named arguments.
- Reverse routing


## Installing

Using composer:

```bash
composer install pkj/router@dev
```

## Setup the router and creating your first route


```php
require "vendor/autoload.php";

use \Pkj\Router\Router,
    \Pkj\Router\Route;



$router = new Router(\Pkj\Router\Request::bindFromHttpFactory());


$router->addRoute(new Route('home', Route::GET, "^/$"), function () {
    echo "Hello World!";
});

```


## A Route

Routes can be added to a router object, given with `new Route('name-of-my-route', Route::GET, '^/some/url$')`.

The following arguments explained:

### Name of the route

The first argument is the name of the route, this is used for giving the route an indentifier so we can `reverse route`
into urls based on the route name.

### Method

The second argument is what HTTP METHOD's that the route should be activated for. It's possible to combine these so
forexample `Route::GET | Route::POST | Route::DELETE` is possible to use because these are bit-flags. This means that
the route will be activated for GET,POST and DELETE requests.

### Url

This is the third argument and tells that the route is assigned to the following URL (in regular expression format).
Urls can be just static like `^/hello/world$`, have anonymous arguments like this `^/hello/(.*?)$` or have named
arguments like this `^/hello/:world$` or even more advanced - a named argument with a regular expression
`^/hello/:<\d+>world$`. As you can see, this is quite flexible.



## Controllers as classes

Normally you would want to create classes that extends a base controller to have access to say forexample the router
so you can access `reverse routing`, etc.

This is possible using the `Router::setControllerFactory` method, it makes it possible to define a way to initialize
the controllers based on callee router found.

Take this example:

```php

class BaseController {
    protected $router;
    public function setRouter (\Pkj\Router\Router $router) {
        $this->router = $router;
    }
}

// Here we use the BaseController.
$router->setControllerFactory(function ($callable, $router) {
    if (is_array($callable)) {
        $controller = new $callable[0];
        if ($controller instanceof BaseController) {
            $controller->setRouter($router);
        }
        // Redefine $callable.
        $callable = array($controller, $callable[1]);
    }
    return $callable;
});

```

The router figures out what `Controller` and `Method` to run - when a factory is set it will filter the callable
through the factory. This means we can create a new instance of the controller and using setters to initialize other
components.


## Routes configuration file

It's also possible to load routes from a normal text file with custom configuration. Note that the syntax is special
to the routes file. It's not following any normal standard like yaml, json and xml because route configuration should
be as easy as possible and logical to read.

Use the `Router::readFile` method to load a routing file like below:

```php

// Read in all the routes.
$router->readFile(__DIR__ . '/routes.txt');

```

The routes file can contain simple route definitions:

```php

%set exact_match 1
home      GET           /                             SomeController.home
home      GET           /blog                         BlogController.all
home      PUT|POST      /blog                         BlogController.create
home      *             /all-methods-allowed-here     SomeController.anyMethod

```

Notice the `%set exact_match 1`, this means that, all urls defined like the `/` needs to be an exact match. It
basically means that you wrap it in regular expression syntax like this: `^/$`, and all routes you define below the set
statement will have this behavior. To disable it you just use `%set exact_match 0` and define routes below.



## Reverse routing

Reverse routing is a mandatory component of any router. It makes it possible to avoid hard-coding URL's in your code.
Instead we use an identifier that we use everywhere.

Standard route with no dynamic arguments:

```php

echo $router->get('home')->reverse();

```

A route with numeric arguments:

```php

echo $router->get('blog_full')->reverse([1337]);

```

A route with named arguments:

```php

echo $router->get('news_full')->reverse(["id" => 1337]);

```


