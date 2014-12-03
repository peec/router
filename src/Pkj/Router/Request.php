<?php
/**
 * Created by PhpStorm.
 * User: pk
 * Date: 03.12.14
 * Time: 10:25
 */

namespace Pkj\Router;


class Request {

    private $url;

    private $basePath;

    private $method;

    private $baseScript;



    static public function bindFromHttpFactory () {

        // Strip path up to correct format.
        if (!isset($_SERVER['PATH_INFO'])) {
            $path = '/';
        } else {
            $path = $_SERVER['PATH_INFO'];
            if (!$path) {
                $path = '/';
            }
        }

        $basePath = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : null;
        $baseScript = basename($basePath);
        $basePath = dirname($basePath);


        $request = new Request();

        $request->setBasePath($basePath);
        $request->setBaseScript($baseScript);
        $request->setMethod(self::bitOfRequestMethod($_SERVER['REQUEST_METHOD']));
        $request->setUrl($path);

        return $request;
    }


    static public function bitOfRequestMethod ($method) {
        switch(strtoupper($method)) {
            case "GET":
                return Route::GET;
                break;
            case "POST":
                return Route::POST;
                break;
            case "DELETE":
                return Route::DELETE;
                break;
            case "PATCH":
                return Route::PATCH;
                break;
            case "PUT":
                return Route::PUT;
                break;
            case "*":
                return Route::WILDCARD;
                break;
        }
    }

    /**
     * @param mixed $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return mixed
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $baseScript
     */
    public function setBaseScript($baseScript)
    {
        $this->baseScript = $baseScript;
    }

    /**
     * @return mixed
     */
    public function getBaseScript()
    {
        return $this->baseScript;
    }


    public function getBaseUrl () {
        return $this->getBasePath() . '/' . $this->getBaseScript();
    }


} 