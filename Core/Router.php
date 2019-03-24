<?php

namespace Core;

use Exception;

/**
 * Router
 */
class Router
{
    /**
     * Associative array of routes (the routing tables)
     * @var array
     */
    private $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    private $params = [];

    /**
     * Add a route to the routing table
     *
     * @param string $route The route URL
     * @param array  $params Parameters (controller, action, etc.)
     *
     * @return void
     */
    public function add($route, $params = [])
    {
        // example: $router->add('{controller}/{id:\d+}/{action}');

        // 1. {controller}\/{id:\d+}\/{action}
        // 2. (?P<controller>[a-z]+)\/{id:\d+}\/(?P<action>[a-z]+)
        // 3. (?P<controller>[a-z]+)\/(?P<id>\d+)\/(?P<action>[a-z]+)
        // 4. /^(?P<controller>[a-z]+)\/(?P<id>\d+)\/(?P<action>[a-z]+)$/i

        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g {controller}
        $route = preg_replace('/\{([a-z-]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Get all routes from the routing table
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the $params property if a route is found
     *
     * @param string $url The route URL
     *
     * @return bool true if a match is found, false otherwise
     */
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {

            if (preg_match($route, $url, $matches)) {

                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;

                return true;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Dispatch the route, creating the controller object and running the action method
     *
     * @param string $url The route URL
     *
     * @return string The URL with the query string variables removed
     * @throws Exception
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {

            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;
            $controller = $controller . 'Controller';

            if (class_exists($controller)) {

                $controllerObject = new $controller($this->params);

                $action = $this->params['action'] . '@Action';
                $action = $this->convertToCamelCase($action);

                $controllerObject->$action();
            } else {
                throw new Exception("Controller class {$controller} not found");
            }
        } else {
            throw new Exception('No route matched.', 404);
        }
    }

    /**
     * Convert the string with hyphens to StudlyCaps,
     * e.g. post-authors => PostAuthors
     *
     * @param string $string The string to convert
     *
     * @return string The string converted to StudlyCaps
     */
    private function convertToStudlyCaps($string)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.g. add-new => addNew
     *
     * @param string $string The string to convert
     *
     * @return string The string converted to camelCase
     */
    private function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /**
     * Remove the query string variables from the URL (if any).
     *
     * @param string $url The full URL
     *
     * @return string $url The URL with the query string variables removed
     */
    private function removeQueryStringVariables($url)
    {
        if ($url !== '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            }
        }

        return $url;
    }

    /**
     * Get the namespace for the controller class. The namespace defined
     * in the route parameters is added if present.
     *
     * @return string $namespace The request URL
     */
    private function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
}
