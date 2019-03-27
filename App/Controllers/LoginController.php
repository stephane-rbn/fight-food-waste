<?php

namespace App\Controllers;

use App\Auth;
use App\Models\User;
use Core\Controller;
use Core\View;

/**
 * Login Controller
 */
class LoginController extends Controller
{
    /**
     * Show the login page
     *
     * @return void
     */
    public function new()
    {
        View::renderTemplate('Login/new.html.twig');
    }

    /**
     * Log in a user
     *
     * @return void
     */
    public function create()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        if ($user) {

            Auth::login($user);

            $this->redirect(Auth::getReturnToPage());

        } else {

            View::renderTemplate('Login/new.html.twig', [
                'email' => $_POST['email'],
            ]);

        }
    }

    public function destroy()
    {
        Auth::logout();

        $this->redirect('/');
    }
}
