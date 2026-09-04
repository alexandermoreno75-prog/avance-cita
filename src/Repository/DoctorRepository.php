<?php
declare(strict_types=1);
namespace App\Repository;
use PDO;
final class DoctorRepository
{
    public function __construct(private PDO $pdo)
    {
    }
    public function active(): array
    {
        return $this->pdo->query(
            'SELECT id, license_number, first_name, last_name, specialty
             FROM doctors
             WHERE active = 1
                 ORDER BY first_name, last_name'
        )->fetchAll();
    }
    public function findActive(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, license_number, first_name, last_name, specialty
             FROM doctors WHERE id = :id AND active = 1 LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $doctor = $statement->fetch();
        return $doctor === false ? null : $doctor;
    }
        public function search(string $term = ''): array
{
    $term = trim($term);

    if ($term === '') {
        return $this->pdo->query(
            'SELECT *
             FROM doctors
             ORDER BY last_name, first_name
             LIMIT 100'
        )->fetchAll();
    }

    $statement = $this->pdo->prepare(
        'SELECT *
         FROM doctors
         WHERE license_number LIKE ?
            OR first_name LIKE ?
            OR last_name LIKE ?
         ORDER BY last_name, first_name
         LIMIT 100'
    );


    $value = '%' . $term . '%';

    $statement->execute([
        $value,
        $value,
        $value,
    ]);

    return $statement->fetchAll();
    }


    public function findByDocument(string $license): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM doctors WHERE license_number = :license_number LIMIT 1'
        );
        $statement->execute(['license_number' => $document]);
        $doctor = $statement->fetch();
        return $doctor === false ? null : $doctor;
    }

}