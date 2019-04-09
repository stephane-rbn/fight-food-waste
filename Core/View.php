<?php

namespace Core;

use App\Auth;
use App\Flash;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * View
 */
class View
{
    /**
     * Render a view file
     *
     * @param string $view The view file
     * @param array $arguments
     *
     * @return void
     */
    public static function render($view, $arguments = [])
    {
        extract($arguments, EXTR_SKIP);

        $file = "../App/Views/{$view}";

        if (is_readable($file )) {
            require $file;
        } else {
            echo "{$file} is not found";
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template The template file
     * @param array $arguments Associative array of data to display in the view (optional)
     *
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function renderTemplate($template, $arguments = [])
    {
        echo self::getTemplate($template, $arguments);
    }

    /**
     * Get the contents of a view template using Twig
     *
     * @param string $template The template file
     * @param array $arguments Associative array of data to display in the view (optional)
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public static function getTemplate($template, $arguments = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
            $twig->addGlobal('current_user', Auth::getUser());
            $twig->addGlobal('flash_messages', Flash::getMessages());
        }

        return $twig->render($template, $arguments);
    }
}
