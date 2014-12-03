<?php
namespace Pkj\Router;

/**
 * Created by PhpStorm.
 * User: pk
 * Date: 28.11.14
 * Time: 16:14
 */

class Router {

    private $routes;

    private $controllers;

    private $request;

    private $controllerFactory;


    /**
     * Creates a new Router.
     *
     * @param Request $request Request object, normally just use the factory on the Request class to generate a full request object based on the PHP environment and user-input.
     */
    public function __construct (Request $request) {
        $this->routes = array();
        $this->request = $request;
    }

    /**
     * Registers a new route to the router.
     *
     * @param Route $route A Route object defining the route
     * @param callable $controller A callable, can be an array of ($objectinstance, 'method') or a anonymous function of choice.
     * @return $this Returns the router object.
     */
    public function addRoute(Route $route, callable $controller) {
        if (isset($this->routes[$route->getName()])) {
            throw new \Exception("Route {$route->getName()} ({$route->getUrl()}) was already registered. Please create a unique name for this route.");
        }
        $route->setRequest($this->request);
        $this->routes[$route->getName()] = $route;
        $this->controllers[$route->getName()] = $controller;
        return $this;
    }


    /**
     * Runs the router.
     *
     * @return bool|Executable returns FALSE if no route was found otherwise an Executable object that can be called.
     */
    public function run () {
        foreach ($this->routes as $index => $route) {
            if ($route->match($this->request) === true) {
                $executable =  new Executable($this->controllers[$index], $route, $this);
                $executable->setFactory($this->controllerFactory);
                return $executable;
            }
        }
        return false;
    }


    /**
     * Returns the route object based on the route name.
     * @param $name The route name.
     * @return \Pkj\Router\Route
     * @throws \Exception
     */
    public function get($name) {
        if (!isset($this->routes[$name])) {
            throw new \Exception("Route $name not added to the router.");
        }
        return $this->routes[$name];
    }



    public function readFile ($file) {
        $file = realpath($file);
        $lines = file($file);

        $settings = array(
            'exact_match' => 0
        );
        foreach ($lines as $lineNumber => $line) {
            $this->parseStringRouteDefinition($file, $lineNumber+1, $line, $settings);
        }
    }

    public function parseStringRouteDefinition ($file, $lineNumber, $str, &$settings) {
        $parts = preg_split('/\s+/', $str);

        $parts = array_filter($parts, function ($item) { return trim ($item);});

        $purpose = 'route';
        if (!$parts || !$parts[0]) {
            $purpose = null;
        }
        if (isset($parts[0][0]) && $parts[0][0] === '#') {
            $purpose = null;
        }
        if (isset($parts[0][0]) && $parts[0] === '%set') {
            $purpose = 'set-setting';
        }

        switch($purpose) {
            case "set-setting":
                $name = $parts[1];
                $val = isset($parts[2]) ? $parts[2] : false;
                $settings[$name] = $val;

                break;
            case "route":
                $name = $parts[0];
                $methods = explode('|',$parts[1]);
                $url = $parts[2];
                $callableDefinition = $parts[3];

                $callable = explode('.', $callableDefinition);

                if (!is_callable($callable)) {
                    throw new RouteStringLineException($file, $lineNumber, "$callableDefinition is not a valid callable. Did you add the class and method?");
                }

                $methodBitNum = 0;
                foreach($methods as $m) {
                    $bit = Request::bitOfRequestMethod($m);
                    if (!$bit) {
                        throw new RouteStringLineException($file, $lineNumber, "$m is not a valid HTTP method, can be methods such as GET, POST, DELETE etc.");
                    } else {
                        $methodBitNum |= $bit;
                    }
                }

                if ($settings['exact_match']) {
                    $url = '^' . $url . '$';
                }


                $this->addRoute(new Route($name, $methodBitNum, $url), $callable);

                break;
            default:
                break;
        }
    }

    public function setControllerFactory (callable $controllerFactory) {
        $this->controllerFactory = $controllerFactory;
    }

}