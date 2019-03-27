<?php

namespace App\Controllers;

use App\Auth;
use Core\Controller;
use Core\View;

/**
 * Products controller
 */
class ProductsController extends Controller
{
    /**
     * Product index
     *
     * @return void
     */
    public function index()
    {
       $this->requireLogin();

        View::renderTemplate('Products/index.html.twig');
    }
}
