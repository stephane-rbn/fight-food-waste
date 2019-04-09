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
 * Password Controller
 */
class PasswordController extends Controller
{
    /**
     * Show the forgotten password page
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function forgot()
    {
        View::renderTemplate('Password/forgot.html.twig');
    }

    /**
     * Send the password reset link to the supplied email
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function requestReset()
    {
        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/reset_requested.html.twig');
    }

    /**
     * Show the reset password form
     *
     * @return void
     * @throws Exception
     */
    public function reset()
    {
        $token = $this->getRouteParams()['token'];

        $user = User::findByPasswordReset($token);

        if ($user) {
            View::renderTemplate('Password/reset.html.twig', [
                'token' => $token,
            ]);
        } else {
            echo 'Password reset token invalid';
        }
    }

    /**
     * Reset the user's password
     *
     * @return void
     * @throws Exception
     */
    public function resetPassword()
    {
        $token = $_POST['token'];

        $user = User::findByPasswordReset($token);

        if ($user) {
            echo "Reset user's password here";
        } else {
            echo 'Password reset token invalid';
        }
    }
}
