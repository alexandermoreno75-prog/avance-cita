<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    /**
     * Genera o recupera el token CSRF.
     */
    public static function token(): string
    {
        self::startSession();

        if (
            !isset($_SESSION['_csrf_token'])
            || !is_string($_SESSION['_csrf_token'])
        ) {
            $_SESSION['_csrf_token'] = bin2hex(
                random_bytes(32)
            );
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Genera el campo hidden para los formularios.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="'
            . htmlspecialchars(
                self::token(),
                ENT_QUOTES,
                'UTF-8'
            )
            . '">';
    }

    /**
     * Valida un token CSRF.
     */
    public static function validate(?string $token): bool
    {
        self::startSession();

        if (
            $token === null
            || !isset($_SESSION['_csrf_token'])
            || !is_string($_SESSION['_csrf_token'])
        ) {
            return false;
        }

        return hash_equals(
            $_SESSION['_csrf_token'],
            $token
        );
    }

    /**
     * Exige un token CSRF válido.
     */
    public static function requireValid(): void
    {
        $token = $_POST['_token'] ?? null;

        if (
            !is_string($token)
            || !self::validate($token)
        ) {
            http_response_code(419);

            self::show419();

            exit;
        }
    }

    /**
     * Mostrar error 419 sin información técnica.
     */
    private static function show419(): void
    {
        $file = dirname(__DIR__, 2)
            . '/views/errors/419.php';

        if (is_file($file)) {
            require $file;
            return;
        }

        echo '<!DOCTYPE html>';
        echo '<html lang="es">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Error 419</title>';
        echo '</head>';
        echo '<body>';
        echo '<h1>419</h1>';
        echo '<h2>Solicitud no válida</h2>';
        echo '<p>La sesión de seguridad ha expirado.</p>';
        echo '<a href="/">Volver al inicio</a>';
        echo '</body>';
        echo '</html>';
    }

    /**
     * Iniciar sesión.
     */
    private static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}