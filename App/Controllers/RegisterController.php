<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;
use Exception;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Register Controller
 */
class RegisterController extends Controller
{
    /**
     * Show the register page
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
            View::renderTemplate('Register/new.html.twig');
        }
    }

    /**
     * Register a new user
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function create()
    {
        $user = new User($_POST);

        if ($user->save()) {
            $user->sendActivationEmail();
            $this->redirect('/register/success');
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
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function success()
    {
        View::renderTemplate('Register/success.html.twig');
    }

    /**
     * Activate a new account
     *
     * @return void
     * @throws Exception
     */
    public function activate()
    {
        User::activateUser($this->getRouteParams()['token']);

        $this->redirect('/register/activated');
    }

    /**
     * Show the activation success page
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function activated()
    {
        View::renderTemplate('Register/activated.html.twig');
    }
}
