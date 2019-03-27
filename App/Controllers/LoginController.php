<?php

namespace App\Controllers;

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

            // Regenerate a new session ID to prevent the site from session fixation attacks
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user->id;

            $this->redirect('/');

        } else {

            View::renderTemplate('Login/new.html.twig', [
                'email' => $_POST['email'],
            ]);

        }
    }

    public function destroy()
    {
        // Unset all of the session variables
        session_unset();

        // Delete the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy the session
        session_destroy();

        $this->redirect('/');
    }
}
