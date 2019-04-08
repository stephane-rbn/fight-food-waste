<?php

namespace App\Controllers;

use Core\Controller;
use Exception;

/**
 * Authenticated controller
 */
abstract class AuthenticatedController extends Controller
{
    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     * @throws Exception
     */
    protected function before()
    {
        $this->requireLogin();
    }
}
