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
     * Buscar médicos por número de licencia, nombre o apellido.
     */
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

    /**
     * Buscar un médico por número de licencia.
     */
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

    /**
     * Registrar un nuevo médico.
     */
    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO doctors
            (
                license_number,
                first_name,
                last_name,
                specialty,
                status
            )
            VALUES
            (
                :license_number,
                :first_name,
                :last_name,
                :specialty,
                :status
            )'
        );

        $statement->execute([
            'license_number' => $data['license_number'],
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'specialty'      => $data['specialty'],
            'status'         => $data['status'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Contar médicos registrados.
     */
    public function count(): int
    {
        return (int) $this->pdo
            ->query('SELECT COUNT(*) FROM doctors')
            ->fetchColumn();
    }

    /**
     * Buscar un médico por ID.
     */
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

    /**
     * Actualizar los datos de un médico.
     */
    public function update(int $id, array $data): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE doctors
             SET
                license_number = :license_number,
                first_name = :first_name,
                last_name = :last_name,
                specialty = :specialty,
                status = :status
             WHERE id = :id'
        );

        return $statement->execute([
            'id'             => $id,
            'license_number' => $data['license_number'],
            'first_name'     => $data['first_name'],
            'last_name'      => $data['last_name'],
            'specialty'      => $data['specialty'],
            'status'         => $data['status'],
        ]);
    }
     public function delete(int $id): bool
     {  
                $statement = $this->pdo->prepare(  
                        'DELETE FROM doctors 
                                WHERE id = :id'  
                                    );  
                                    return $statement->execute([  
                                            'id' => $id 
                                                ]); }
            
    }
