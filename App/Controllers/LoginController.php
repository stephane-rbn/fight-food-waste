<?php

namespace App\Controllers;

use App\Auth;
use App\Flash;
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

            Flash::addMessage('Login successful');

            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage('Login unsuccessful, please try again');

            View::renderTemplate('Login/new.html.twig', [
                'email' => $_POST['email'],
            ]);

        }
    }

    /**
     * Log out a user
     *
     * @return void
     */
    public function destroy()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message and redirect to homepage
     *
     * @return void
     */
    public function showLogoutMessage()
    {
        Flash::addMessage('Logout successful');

        $this->redirect('/');
    }
}
