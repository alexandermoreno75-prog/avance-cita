<?php

declare(strict_types=1);

namespace App\Core;

use ErrorException;
use Throwable;

final class ErrorHandler
{
    public static function register(): void
    {
        // Capturar excepciones no controladas
        set_exception_handler(
            function (Throwable $exception): void {
                self::log($exception);

                http_response_code(500);

                self::renderErrorPage('500');
            }
        );

        // Convertir errores de PHP en excepciones
        set_error_handler(
            function (
                int $severity,
                string $message,
                string $file,
                int $line
            ): bool {
                if (!(error_reporting() & $severity)) {
                    return false;
                }

                throw new ErrorException(
                    $message,
                    0,
                    $severity,
                    $file,
                    $line
                );
            }
        );

        // Capturar errores fatales
        register_shutdown_function(
            function (): void {
                $error = error_get_last();

                if ($error === null) {
                    return;
                }

                $fatalTypes = [
                    E_ERROR,
                    E_PARSE,
                    E_CORE_ERROR,
                    E_COMPILE_ERROR
                ];

                if (!in_array($error['type'], $fatalTypes, true)) {
                    return;
                }

                self::logFatal($error);

                http_response_code(500);

                self::renderErrorPage('500');
            }
        );
    }

    /**
     * Mostrar página de error.
     */
    public static function renderErrorPage(string $code): void
    {
        $allowedCodes = [
            '403',
            '404',
            '419',
            '500'
        ];

        if (!in_array($code, $allowedCodes, true)) {
            $code = '500';
        }

        $file = dirname(__DIR__, 2)
            . '/views/errors/'
            . $code
            . '.php';

        if (is_file($file)) {
            require $file;
            return;
        }

        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Error ' . htmlspecialchars($code) . '</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>Error ' . htmlspecialchars($code) . '</h1>';
        echo '<p>Ha ocurrido un error inesperado.</p>';
        echo '</body>';
        echo '</html>';
    }

    /**
     * Guardar errores en storage/logs/app.log
     */
    public static function log(Throwable $exception): void
    {
        $logDirectory = dirname(__DIR__, 2)
            . '/storage/logs';

        self::createLogDirectory($logDirectory);

        $message = sprintf(
            "[%s] %s: %s en %s:%d\n%s\n\n",
            date('Y-m-d H:i:s'),
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );

        error_log(
            $message,
            3,
            $logDirectory . '/app.log'
        );
    }

    /**
     * Guardar errores fatales.
     */
    private static function logFatal(array $error): void
    {
        $logDirectory = dirname(__DIR__, 2)
            . '/storage/logs';

        self::createLogDirectory($logDirectory);

        $message = sprintf(
            "[%s] ERROR FATAL: %s en %s:%d\n\n",
            date('Y-m-d H:i:s'),
            $error['message'] ?? 'Error fatal desconocido',
            $error['file'] ?? 'Archivo desconocido',
            $error['line'] ?? 0
        );

        error_log(
            $message,
            3,
            $logDirectory . '/app.log'
        );
    }

    /**
     * Crear carpeta de logs.
     */
    private static function createLogDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }
}