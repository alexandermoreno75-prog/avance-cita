<?php

declare(strict_types=1);

$password = 'Admin12345';

$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

echo '<h2>Hash generado</h2>';

echo '<p><strong>Contraseña:</strong> ' . htmlspecialchars($password) . '</p>';

echo '<p><strong>Hash:</strong></p>';

echo '<textarea rows="4" cols="80">'
    . htmlspecialchars($hash)
    . '</textarea>';

