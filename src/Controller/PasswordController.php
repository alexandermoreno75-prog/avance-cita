<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repository\UserRepository;

final class PasswordController
{
    public function __construct(
        private UserRepository $users
    ) {
    }


    /**
     * Mostrar formulario para cambiar contraseña.
     */
    public function edit(): void
    {
        Auth::requireLogin();

        View::render(
            'auth/change-password',
            [
                'title' => 'Cambiar contraseña',
                'errors' => [],
            ]
        );
    }


    /**
     * Procesar cambio de contraseña.
     */
    public function update(): void
    {
        Auth::requireLogin();

        Csrf::requireValid(
            $_POST['_token'] ?? null
        );


        /*
        |--------------------------------------------------------------------------
        | Datos enviados por el formulario
        |--------------------------------------------------------------------------
        */

        $currentPassword = (string) (
            $_POST['current_password'] ?? ''
        );

        $newPassword = (string) (
            $_POST['new_password'] ?? ''
        );

        $confirmPassword = (string) (
            $_POST['new_password_confirmation'] ?? ''
        );


        $errors = [];


        /*
        |--------------------------------------------------------------------------
        | Validar contraseña actual
        |--------------------------------------------------------------------------
        */

        if ($currentPassword === '') {
            $errors['current_password'] =
                'Debe ingresar su contraseña actual.';
        }


        /*
        |--------------------------------------------------------------------------
        | Validar nueva contraseña
        |--------------------------------------------------------------------------
        */

        if ($newPassword === '') {

            $errors['new_password'] =
                'Debe ingresar una nueva contraseña.';

        } elseif (strlen($newPassword) < 8) {

            $errors['new_password'] =
                'La nueva contraseña debe tener mínimo 8 caracteres.';

        } elseif (strlen($newPassword) > 72) {

            $errors['new_password'] =
                'La nueva contraseña no puede superar 72 caracteres.';
        }


        /*
        |--------------------------------------------------------------------------
        | Confirmar nueva contraseña
        |--------------------------------------------------------------------------
        */

        if ($confirmPassword === '') {

            $errors['new_password_confirmation'] =
                'Debe confirmar la nueva contraseña.';

        } elseif (
            $newPassword !== $confirmPassword
        ) {

            $errors['new_password_confirmation'] =
                'Las contraseñas nuevas no coinciden.';
        }


        /*
        |--------------------------------------------------------------------------
        | Si existen errores, mostrar formulario nuevamente
        |--------------------------------------------------------------------------
        */

        if ($errors !== []) {

            View::render(
                'auth/change-password',
                [
                    'title' => 'Cambiar contraseña',
                    'errors' => $errors,
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Obtener ID del usuario autenticado
        |--------------------------------------------------------------------------
        */

        $userId = Auth::id();

        if ($userId === null) {

            http_response_code(401);

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Buscar usuario
        |--------------------------------------------------------------------------
        */

        $user = $this->users->findById(
            $userId
        );
       
        if ($user === null) {

           http_response_code(404);

          View::render('errors/404', [
          'title' => 'Página no encontrada'
          ] );

          exit;

        /*
        |--------------------------------------------------------------------------
        | Verificar contraseña actual
        |--------------------------------------------------------------------------
        */

        if (
            !password_verify(
                $currentPassword,
                $user['password_hash']
            )
        ) {

            View::render(
                'auth/change-password',
                [
                    'title' => 'Cambiar contraseña',

                    'errors' => [
                        'current_password' =>
                            'La contraseña actual es incorrecta.',
                    ],
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Evitar utilizar la misma contraseña
        |--------------------------------------------------------------------------
        */

        if (
            password_verify(
                $newPassword,
                $user['password_hash']
            )
        ) {

            View::render(
                'auth/change-password',
                [
                    'title' => 'Cambiar contraseña',

                    'errors' => [
                        'new_password' =>
                            'La nueva contraseña debe ser diferente a la actual.',
                    ],
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Crear hash seguro
        |--------------------------------------------------------------------------
        */

        $passwordHash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );


        /*
        |--------------------------------------------------------------------------
        | Actualizar contraseña
        |--------------------------------------------------------------------------
        */

        $updated = $this->users->updatePassword(
            $userId,
            $passwordHash
        );


        if (!$updated) {

            View::render(
                'auth/change-password',
                [
                    'title' => 'Cambiar contraseña',

                    'errors' => [
                        'general' =>
                            'No fue posible actualizar la contraseña.',
                    ],
                ]
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Mensaje de éxito
        |--------------------------------------------------------------------------
        */

        flash(
            'success',
            'La contraseña fue cambiada correctamente.'
        );

        redirect('/');
    }
}
}