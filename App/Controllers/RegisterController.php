<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

/**
 *
 * Register Controller
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
            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/register/success', true, 303);
            exit;
        } else {
            s($user->getErrors());
            View::renderTemplate('Register/new.html.twig', [
                'user' => $user,
            ]);
        }
    }

    /**
     * Show the register success page
     *
     * @return void
     */
    public function success()
    {
        View::renderTemplate('Register/success.html.twig');
    }
}
