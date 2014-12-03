<?php
/**
 * Created by PhpStorm.
 * User: pk
 * Date: 28.11.14
 * Time: 16:46
 */

namespace Pkj\Router;


class Executable {

    private $factory;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var Router
     */
    private $router;


    function __construct(callable $callable, Route $route, Router $router) {
        $this->callable = $callable;
        $this->route = $route;
        $this->router = $router;
    }

    public function setFactory(callable $factory = null) {
        $this->factory = $factory;
    }


    public function execute () {
        if ($this->factory) {
            $callable = call_user_func_array($this->factory, [$this->callable, $this->router]);
        } else {
            $callable = $this->callable;
        }
        return call_user_func_array($callable, $this->route->getArguments());
    }

} 