<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class DoctorRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function search(string $term = ''): array
    {
        if ($term === '') {
            return $this->pdo
                ->query(
                    'SELECT *
                     FROM doctors
                     ORDER BY last_name, first_name
                     LIMIT 100'
                )
                ->fetchAll();
        }

        $statement = $this->pdo->prepare(
            'SELECT *
             FROM doctors
             WHERE license_number LIKE :term
                OR first_name LIKE :term
                OR last_name LIKE :term
             ORDER BY last_name, first_name
             LIMIT 100'
        );

        $statement->execute([
            'term' => '%' . $term . '%'
        ]);

        return $statement->fetchAll();
    }

    public function findByDocument(string $document): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
             FROM doctors
             WHERE license_number = :document
             LIMIT 1'
        );

        $statement->execute([
            'document' => $document
        ]);

        $doctor = $statement->fetch();

        return $doctor === false ? null : $doctor;
    }

    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO doctors
                (license_number, first_name, last_name, specialty, active)
             VALUES
                (:license_number, :first_name, :last_name, :specialty, :active)'
        );

        $statement->execute([
            'license_number' => $data['license_number'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'specialty' => $data['specialty'],
            'active' => $data['active'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function count(): int
    {
        return (int) $this->pdo
            ->query('SELECT COUNT(*) FROM doctors')
            ->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT *
             FROM doctors
             WHERE id = :id
             LIMIT 1'
        );

        $statement->execute([
            'id' => $id
        ]);

        $doctor = $statement->fetch();

        return $doctor === false ? null : $doctor;
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE doctors
             SET license_number = :license_number,
                 first_name = :first_name,
                 last_name = :last_name,
                 specialty = :specialty,
                 active = :active
             WHERE id = :id'
        );

        return $statement->execute([
            'license_number' => $data['license_number'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'specialty' => $data['specialty'],
            'active' => $data['active'],
            'id' => $id,
        ]);
    }
}