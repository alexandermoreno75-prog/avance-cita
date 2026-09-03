<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class DoctorRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Obtener todos los doctores activos.
     */
    public function active(): array
    {
        return $this->pdo->query(
            'SELECT id, license_number, first_name, last_name, specialty
             FROM doctors
             WHERE active = 1
             ORDER BY first_name, last_name'
        )->fetchAll();
    }

    /**
     * Buscar doctores activos.
     */
    public function search(string $search): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, license_number, first_name, last_name, specialty
             FROM doctors
             WHERE active = 1
             AND (
                 first_name LIKE :search
                 OR last_name LIKE :search
                 OR license_number LIKE :search
                 OR specialty LIKE :search
             )
             ORDER BY first_name, last_name'
        );

        $statement->execute([
            'search' => '%' . $search . '%'
        ]);

        return $statement->fetchAll();
    }

    /**
     * Buscar un doctor activo por ID.
     */
    public function findActive(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, license_number, first_name, last_name, specialty
             FROM doctors
             WHERE id = :id
             AND active = 1
             LIMIT 1'
        );

        $statement->execute([
            'id' => $id
        ]);

        $doctor = $statement->fetch();

        return $doctor === false ? null : $doctor;
    }

    /**
     * Crear un doctor.
     */
    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO doctors
             (license_number, first_name, last_name, specialty, active)
             VALUES
             (:license_number, :first_name, :last_name, :specialty, :active)'
        );

        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Contar todos los doctores.
     */
    public function count(): int
    {
        return (int) $this->pdo
            ->query('SELECT COUNT(*) FROM doctors')
            ->fetchColumn();
    }
}
