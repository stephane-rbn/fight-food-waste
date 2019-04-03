<?php

namespace App;

use App\Models\RememberLogin;
use App\Models\User;
use Exception;

/**
 * Authentication class
 */
class Auth
{
    /**
     * Log in the user
     *
     * @param User $user The user model
     * @param boolean $rememberMe Remember the login if true
     *
     * @return void
     * @throws Exception
     */
    public static function login($user, $rememberMe)
    {
        // Regenerate a new session ID to prevent the site from session fixation attacks
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if ($rememberMe) {
           if ($user->rememberLogin()) {
               setcookie('remember_me', $user->rememberToken, $user->expiryTimestamp, '/');
           }
        }
    }

    /**
     * Log out the user
     *
     * @return void
     * @throws Exception
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

        // Destroy remember me cookie
        self::forgetLogin();
    }

    /**
     * Remember the originally requested page in the session
     *
     * @return void
     */
    public static function rememberRequestedPage()
    {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the originally requested page to return to after requiring login, or default to the homepage
     *
     * @return string
     */
    public static function getReturnToPage()
    {
        return $_SESSION['return_to'] ?? '/';
    }

    /**
     * Get the current logged-in user, from the session or the remember-me cookie
     *
     * @return mixed The user model or null if not logged in
     * @throws Exception
     */
    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        } else {
            return self::loginFromRememberCookie();
        }
    }

    /**
     * Login the user from a remembered login token
     *
     * @return mixed The user model if login cookie found; null otherwise
     * @throws Exception
     */
    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {
            $rememberedLogin = RememberLogin::findByToken($cookie);

            if ($rememberedLogin && !$rememberedLogin->hasExpired()) {
                $user = $rememberedLogin->getUser();

                self::login($user, false);

                return $user;
            }
        }
    }

    /**
     * Forget the remembered login, if present
     *
     * @return void
     * @throws Exception
     */
    protected static function forgetLogin()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {
            $rememberLogin = RememberLogin::findByToken($cookie);

            if ($rememberLogin) {
                $rememberLogin->delete();
            }

            setcookie('remember_me', '', time() - 3600);
        }
    }
}
