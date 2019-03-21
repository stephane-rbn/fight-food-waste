<?php

namespace App\Controllers;

use App\Models\User;
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
    /**
     * Show the register page
     *
     * @return void
     */
    public function new()
    {
        View::renderTemplate('Register/new.html.twig');
    }

    /**
     * Register a new user
     *
     * @return void
     */
    public function create()
    {
        $user = new User($_POST);

        if ($user->save()) {
            View::renderTemplate('Register/success.html.twig');
        } else {
            s($user->getErrors());
            View::renderTemplate('Register/new.html.twig', [
                'user' => $user,
            ]);
        }
    }
}
