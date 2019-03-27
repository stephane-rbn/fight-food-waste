<?php

namespace App;

use App\Models\User;

/**
 * Authentication class
 */
class Auth
{
    /**
     * Log in the user
     *
     * @param User $user The user model
     *
     * @return void
     */
    public static function login($user)
    {
        // Regenerate a new session ID to prevent the site from session fixation attacks
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;
    }

    /**
     * Log out the user
     *
     * @return void
     */
    public static function logout()
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
    }

    /**
     * Return indicator of whether a user is logged in or not
     *
     * @return bool
     */
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
