<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PatientRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    
    public function paginate(
        string $term,
        int $page,
        int $perPage
    ): array {
        $offset = ($page - 1) * $perPage;

        $params = [];
        $where = '';

        if ($term !== '') {
            $where = 'WHERE document_number LIKE :term
                      OR first_name LIKE :term
                      OR last_name LIKE :term';

            $params['term'] = '%' . $term . '%';
        }

       
        $countStmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM patients {$where}"
        );

        $countStmt->execute($params);

        $total = (int) $countStmt->fetchColumn();

        
        $sql = "SELECT *
                FROM patients
                {$where}
                ORDER BY last_name, first_name
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        if ($term !== '') {
            $stmt->bindValue(
                ':term',
                $params['term'],
                PDO::PARAM_STR
            );
        }

        $stmt->bindValue(
            ':limit',
            $perPage,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':offset',
            $offset,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
        ];
    }

   
    public function count(): int
    {
        $stmt = $this->pdo->query(
            'SELECT COUNT(*) FROM patients'
        );

        return (int) $stmt->fetchColumn();
    }

    
    public function findByDocument(string $document): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM patients
             WHERE document_number = :document
             LIMIT 1'
        );

        $stmt->execute([
            'document' => $document,
        ]);

        $patient = $stmt->fetch();

        return $patient === false ? null : $patient;
    }
}