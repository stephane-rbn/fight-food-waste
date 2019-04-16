<?php

namespace App\Controllers;

use App\Auth;
use App\Flash;
use Core\View;
use Exception;
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

    /**
     * Update the profile
     *
     * @return void
     * @throws Exception
     */
    public function update()
    {
        $user = Auth::getUser();

        if ($user->updateProfile($_POST)) {
            Flash::addMessage('Changes saved');
            $this->redirect('/profile/show');
        } else {
            View::renderTemplate('Profile/edit.html.twig', [
                'user' => $user,
            ]);
        }
    }
}
