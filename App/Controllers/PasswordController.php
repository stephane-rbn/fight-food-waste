<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\View;

/**
 * Password Controller
 */
class PasswordController extends Controller
{
    /**
     * Show the forgotten password page
     *
     * @return void
     */
    public function forgot()
    {
        View::renderTemplate('Password/forgot.html.twig');
    }

    /**
     * Send the password reset link to the supplied email
     *
     * @return void
     */
    public function requestReset()
    {
        User::sendPasswordReset($_POST['email']);

        View::renderTemplate('Password/reset_requested.html.twig');
    }
}
