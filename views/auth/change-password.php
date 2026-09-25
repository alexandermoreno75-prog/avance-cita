<?php

declare(strict_types=1);

$errors = $errors ?? [];
?>

<section class="page-header">
    <div>
        <h1>Cambiar contraseña</h1>
        <p>Actualice de forma segura la contraseña de su cuenta.</p>
    </div>
</section>

<section class="card">
    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form
        method="POST"
        action="<?= e(url('/change-password')) ?>"
    >
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="current_password">
                Contraseña actual
            </label>

            <input
                type="password"
                id="current_password"
                name="current_password"
                required
                autocomplete="current-password"
            >
        </div>

        <div class="form-group">
            <label for="new_password">
                Nueva contraseña
            </label>

            <input
                type="password"
                id="new_password"
                name="new_password"
                required
                minlength="8"
                autocomplete="new-password"
            >

            <small>
                La contraseña debe tener mínimo 8 caracteres.
            </small>
        </div>

        <div class="form-group">
            <label for="new_password_confirmation">
                Confirmar nueva contraseña
            </label>

            <input
                type="password"
                id="new_password_confirmation"
                name="new_password_confirmation"
                required
                minlength="8"
                autocomplete="new-password"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="button primary">
                Cambiar contraseña
            </button>

            <a
                href="<?= e(url('/')) ?>"
                class="button"
            >
                Cancelar
            </a>
        </div>
    </form>
</section>
