<?php

use Core\Controller;

/**
 * Authenticated controller
 */
abstract class AuthenticatedController extends Controller
{
    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    protected function before()
    {
        $this->requireLogin();
    }
}
