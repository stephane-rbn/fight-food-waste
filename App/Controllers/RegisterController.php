<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

/**
 *
 * Register Controller
 *
 * PHP Version 7.2
 *
 */
class RegisterController extends Controller
{
    public function new()
    {
        View::renderTemplate('Register/new.html.twig');
    }
}
