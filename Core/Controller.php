<?php

namespace Core;

use Exception;

/**
 * Base Controller
 */
abstract class Controller
{
    /**
     * Parameters from the matched route
     * @var array
     */
    private $routeParams = [];

    public function __construct($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * Called when an action method is not found (either doesn't exist or not public)
     *
     * @param string $method Method name
     * @param array $arguments Arguments passed to the method
     *
     * @return void
     * @throws Exception
     */
    public function __call($method, $arguments)
    {
        $method = preg_replace('/@Action/', '', $method);

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $arguments);
                $this->after();
            }
        } else {
            throw new Exception("Method {$method} not found in controller " . get_class($this));
        }
    }

    /**
     * Get parameters from the matched route
     *
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before() {}

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after() {}
}
