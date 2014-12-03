<?php
/**
 * Created by PhpStorm.
 * User: pk
 * Date: 28.11.14
 * Time: 16:17
 */

namespace Pkj\Router;


class Route {

    const GET = 1;
    const POST = 2;
    const DELETE = 4;
    const PATCH = 8;
    const PUT = 16;

    const WILDCARD = 31;


    private $name;

    private $method;

    private $url;

    private $builtArguments;

    /**
     * @var Request
     */
    private $request;


    const NAMED_ARG_SYMBOL = ':';
    const REGEX_ARG_START = '(';

    public function __construct($name, $method, $url) {
        $this->method = $method;
        $this->url = $url;
        $this->name = $name;
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

    public function getArguments () {
        return $this->builtArguments;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }




    public function reverse (array $args = array()) {
        $url = $this->url;

        // Remove regexp peaces..
        if (substr($url, 0, 1) == '^') {
            $url = substr($url, 1);
        }
        if (substr($url, -1) == '$') {
            $url = substr($url, 0, -1);
        }


        $bits = explode('/', $url);

        $argIndex = 0;
        foreach ($bits as $bitKey => $bit) {
            $startBit = substr($bit, 0, 1);
            // is dynamic argument.
            if (in_array($startBit, [self::NAMED_ARG_SYMBOL, self::REGEX_ARG_START])) {
                $isNamed = $startBit === self::NAMED_ARG_SYMBOL;

                $value = null;
                if ($isNamed) {
                    $namedIndex = substr($bit, 1);
                    $namedIndex = preg_replace('(<.*?>)', '', $namedIndex);
                    $value = $args[$namedIndex];
                } else {
                    $value = $args[$argIndex];
                }

                $bits[$bitKey] = $value;

                $argIndex++;
            }
        }

        $url = $this->request->getBaseUrl() . implode('/', $bits);


        return $url;
    }




    public function getPattern () {

        // Normalize pattern.
        $bits = explode('/', $this->url);
        foreach ($bits as $bitKey => $bit) {
            $startBit = substr($bit, 0, 1);
            // Needs to be converted to regex.
            if ($startBit === self::NAMED_ARG_SYMBOL) {
                $pattern = '.*?';
                $matches = array();
                if (preg_match("#:<(.*?)>#", $bit, $matches)) {
                    $pattern = $matches[1];
                }

                $bits[$bitKey] = "($pattern)";
            }
        }


        $pattern = "#".implode('/', $bits)."#";

        return $pattern;
    }

    public function match (Request $request) {
        $builtArgs = array();

        $matched = $this->getMethod() & $request->getMethod() &&
            preg_match($this->getPattern(), $request->getUrl(), $builtArgs);

        if ($matched) {
            unset($builtArgs[0]);
            $this->builtArguments = array_values($builtArgs);
        }

        return $matched;
    }
} 