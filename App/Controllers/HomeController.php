<?php

namespace App\Controllers;

use App\Mail;
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
        // Mail::send('rabena.stephane@gmail.com', 'Test', 'This is a test', '<h1>This is a test</h1>');

        View::renderTemplate('Home/index.html.twig');
    }
}
