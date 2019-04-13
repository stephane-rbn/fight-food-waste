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

        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/reset.html.twig', [
            'token' => $token,
        ]);
    }

    /**
     * Reset the user's password after submission
     *
     * @return void
     * @throws Exception
     */
    public function resetPassword()
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetUserPassword($_POST['password'])) {
            echo "Password valid";
        } else {
            View::renderTemplate('Password/reset.html.twig', [
                'token' => $token,
                'user' => $user,
            ]);
        }
    }

    /**
     * Find the user model associated with the password reset token, or end the request with a message
     *
     * @param string $token
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     * @throws Exception
     */
    private function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {
            return $user;
        } else {
            View::renderTemplate('Password/token_expired.html.twig');
            exit;
        }
    }
}
