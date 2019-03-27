<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

/**
 * Home Controller
 */
class HomeController extends Controller
{
    /**
     * Before filter
     *
     * @return void
     */
    protected function before() {}

    /**
     * After filter
     *
     * @return void
     */
    protected function after() {}

    /**
     * Show the index page
     *
     * @return void
     */
    public function index()
    {
        View::renderTemplate('Home/index.html.twig');
    }
}
