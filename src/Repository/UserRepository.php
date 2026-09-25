<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findActiveByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password_hash, role
             FROM users
             WHERE email = :email
               AND active = 1
             LIMIT 1'
        );

        $stmt->execute([
            'email' => mb_strtolower(trim($email)),
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password_hash, role
             FROM users
             WHERE id = :id
             LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function updatePassword(
        int $id,
        string $passwordHash
    ): bool {
        $stmt = $this->pdo->prepare(
            'UPDATE users
             SET password_hash = :password_hash
             WHERE id = :id'
        );

        return $stmt->execute([
            'password_hash' => $passwordHash,
            'id' => $id,
        ]);
    }
}