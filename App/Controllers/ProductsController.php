<?php

namespace App\Controllers;

use AuthenticatedController;
use Core\View;

/**
 * Products controller
 */
class ProductsController extends AuthenticatedController
{
    /**
     * Product index
     *
     * @return void
     */
    public function index()
    {
        View::renderTemplate('Products/index.html.twig');
    }
}
