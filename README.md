# PHP Router Component

Allows your project to integration a flexible `Router` component.



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


