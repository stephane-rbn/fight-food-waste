<?php

namespace App\Controllers;

use App\Auth;
use App\Flash;
use App\Models\User;
use Core\Controller;
use Core\View;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Login Controller
 */
class LoginController extends Controller
{
    /**
     * Show the login page
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function new()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/');
        } else {
            View::renderTemplate('Login/new.html.twig');
        }
    }

    /**
     * Log in a user
     *
     * @return void
     * @throws Exception
     */
    public function create()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        $rememberMe = isset($_POST['rememberMe']);

        if ($user) {
            Auth::login($user, $rememberMe);

            Flash::addMessage('Login successful');

            $this->redirect(Auth::getReturnToPage());
        } else {
            Flash::addMessage('Login unsuccessful, please try again', Flash::WARNING);

            View::renderTemplate('Login/new.html.twig', [
                'email' => $_POST['email'],
                'remember_me' => $rememberMe,
            ]);
        }
    }

    /**
     * Log out a user
     *
     * @return void
     * @throws Exception
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
