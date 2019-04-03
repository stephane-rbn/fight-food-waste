<?php

namespace Core;

use App\Config;
use ErrorException;
use Exception;

/**
 * Error and exception handler
 */
class Error
{
    /**
     * Error handler. Convert all errors to Exceptions by throwing an ErrorException.
     *
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file Filename the error raised in
     * @param int $line Line number in the file
     *
     * @return void
     * @throws ErrorException
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler
     *
     * @param Exception $exception The exception
     *
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        // Code is 404 (not found) or 500 (general server error)
        $httpCode = $exception->getCode();

        if ($httpCode !== 404) {
            $httpCode = 500;
        }

        http_response_code($httpCode);

        // Write error message in log file (and display in browser if configured)
        if (Config::showErrors()) {
            echo '<h1>Fatal error</h1>';
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: <b>'" . $exception->getMessage() . "'</b></p>";
            echo '<p>Stack trace:<pre>' . $exception->getTraceAsString() . '</pre></p>';
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . '</p>';
        }

        date_default_timezone_set('GMT');

        $logFilename = dirname(__DIR__) . '/logs/logfile_' . date('Ymd') . '.log';

        // Set the location of the file where error messages will be saved
        ini_set('error_log', $logFilename);

        $message = "Uncaught exception: '" . get_class($exception) . "'";
        $message .= ' with message "' . $exception->getMessage() . '"';
        $message .= "\nStack trace: " . $exception->getTraceAsString();
        $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

        // Write message to that file
        error_log($message);

        View::renderTemplate($httpCode . '.html.twig');
    }
}
