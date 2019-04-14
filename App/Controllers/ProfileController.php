<?php

namespace App\Controllers;

use App\Auth;
use Core\View;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Profile controller
 */
class ProfileController extends AuthenticatedController
{
    /**
     * Show the profile
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        View::renderTemplate('Profile/show.html.twig', [
            'user' => Auth::getUser(),
        ]);
    }

    /**
     * Show the form for editing the profile
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function edit()
    {
        View::renderTemplate('Profile/edit.html.twig', [
            'user' => Auth::getUser(),
        ]);
    }
}
