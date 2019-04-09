<?php

namespace App\Controllers;

use Core\View;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Products controller
 */
class ProductsController extends AuthenticatedController
{
    /**
     * Product index
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index()
    {
        View::renderTemplate('Products/index.html.twig');
    }
}
