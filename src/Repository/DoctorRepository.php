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
}