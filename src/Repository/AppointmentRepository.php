<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class AppointmentRepository
{
    
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Listar citas.
     */
    public function list(string $document = ''): array
    {
        $sql = '
            SELECT
                a.id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                p.document_number,
                p.first_name AS patient_first_name,
                p.last_name AS patient_last_name,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name,
                r.code AS room_code
            FROM appointments a
            JOIN patients p ON p.id = a.patient_id
            JOIN doctors d ON d.id = a.doctor_id
            JOIN rooms r ON r.id = a.room_id
        ';

        $params = [];

        if ($document !== '') {
            $sql .= ' WHERE p.document_number = :document';
            $params['document'] = $document;
        }

        $sql .= '
            ORDER BY a.appointment_date DESC,
                     a.appointment_time DESC
            LIMIT 150
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return $statement->fetchAll();
    }

    /**
     * Buscar una cita por ID.
     */
    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            '
            SELECT
                a.*,
                p.document_type,
                p.document_number,
                p.first_name AS patient_first_name,
                p.last_name AS patient_last_name,
                d.license_number,
                d.first_name AS doctor_first_name,
                d.last_name AS doctor_last_name,
                d.specialty,
                r.code AS room_code,
                r.name AS room_name,
                creator.name AS creator_name,
                canceller.name AS canceller_name,
                completer.name AS completer_name
            FROM appointments a
            JOIN patients p ON p.id = a.patient_id
            JOIN doctors d ON d.id = a.doctor_id
            JOIN rooms r ON r.id = a.room_id
            JOIN users creator ON creator.id = a.created_by
            LEFT JOIN users canceller ON canceller.id = a.cancelled_by
            LEFT JOIN users completer ON completer.id = a.completed_by
            WHERE a.id = :id
            LIMIT 1
            '
        );

        $statement->execute([
            'id' => $id
        ]);

        $appointment = $statement->fetch();

        return $appointment === false ? null : $appointment;
    }

    /**
     * Obtener horas ocupadas para un médico o consultorio.
     */
    public function occupiedTimes(
        string $date,
        int $doctorId,
        int $roomId
    ): array {
        $statement = $this->pdo->prepare(
            "
            SELECT appointment_time
            FROM appointments
            WHERE appointment_date = :date
              AND status IN ('requested', 'confirmed')
              AND (
                    doctor_id = :doctor_id
                    OR room_id = :room_id
              )
            "
        );

        $statement->execute([
            'date' => $date,
            'doctor_id' => $doctorId,
            'room_id' => $roomId,
        ]);

        return array_column(
            $statement->fetchAll(),
            'appointment_time'
        );
    }

    
    public function hasConflict(
        string $date,
        string $time,
        int $patientId,
        int $doctorId,
        int $roomId
    ): bool {
        $statement = $this->pdo->prepare(
            "
            SELECT COUNT(*)
            FROM appointments
            WHERE appointment_date = :date
              AND appointment_time = :time
              AND status IN ('requested', 'confirmed')
              AND (
                    patient_id = :patient_id
                    OR doctor_id = :doctor_id
                    OR room_id = :room_id
              )
            "
        );

        $statement->execute([
            'date' => $date,
            'time' => $time,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'room_id' => $roomId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

    /**
     * Verificar conflictos al reprogramar una cita.
     *
     * Excluye la propia cita mediante $appointmentId.
     */
    public function hasConflictForReschedule(
        int $appointmentId,
        string $date,
        string $time,
        int $doctorId,
        int $roomId
    ): bool {
        $statement = $this->pdo->prepare(
            "
            SELECT COUNT(*)
            FROM appointments
            WHERE appointment_date = :date
              AND appointment_time = :time
              AND id <> :appointment_id
              AND status IN ('requested', 'confirmed')
              AND (
                    doctor_id = :doctor_id
                    OR room_id = :room_id
              )
            "
        );

        $statement->execute([
            'date' => $date,
            'time' => $time,
            'appointment_id' => $appointmentId,
            'doctor_id' => $doctorId,
            'room_id' => $roomId,
        ]);

        return (int) $statement->fetchColumn() > 0;
    }

   
    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            "
            INSERT INTO appointments
            (
                patient_id,
                doctor_id,
                room_id,
                appointment_date,
                appointment_time,
                notes,
                created_by,
                status
            )
            VALUES
            (
                :patient_id,
                :doctor_id,
                :room_id,
                :appointment_date,
                :appointment_time,
                :notes,
                :created_by,
                :status
            )
            "
        );

        if (!isset($data['status'])) {
            $data['status'] = 'requested';
        }

        $statement->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    
    public function cancel(int $id, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            "
            UPDATE appointments
            SET
                status = 'cancelled',
                cancelled_by = :user_id,
                cancelled_at = NOW()
            WHERE id = :id
              AND status IN ('requested', 'confirmed')
            "
        );

        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() === 1;
    }

    
    public function complete(int $id, int $userId): bool
    {
        $statement = $this->pdo->prepare(
            "
            UPDATE appointments
            SET
                status = 'attended',
                completed_by = :user_id,
                completed_at = NOW()
            WHERE id = :id
              AND status = 'confirmed'
              AND TIMESTAMP(appointment_date, appointment_time) <= NOW()
            "
        );

        $statement->execute([
            'id' => $id,
            'user_id' => $userId,
        ]);

        return $statement->rowCount() === 1;
    }

   
    public function countScheduled(): int
    {
        return (int) $this->pdo
            ->query(
                "
                SELECT COUNT(*)
                FROM appointments
                WHERE status IN ('requested', 'confirmed')
                "
            )
            ->fetchColumn();
    }

    
    public function countToday(): int
    {
        return (int) $this->pdo
            ->query(
                "
                SELECT COUNT(*)
                FROM appointments
                WHERE appointment_date = CURRENT_DATE
                  AND status IN ('requested', 'confirmed')
                "
            )
            ->fetchColumn();
    }

   
    public function reschedule(
        int $appointmentId,
        int $doctorId,
        int $roomId,
        string $date,
        string $time,
        ?string $notes = null
    ): bool {
        $statement = $this->pdo->prepare(
            "
            UPDATE appointments
            SET
                doctor_id = :doctor_id,
                room_id = :room_id,
                appointment_date = :appointment_date,
                appointment_time = :appointment_time,
                notes = :notes
            WHERE id = :id
              AND status IN ('requested', 'confirmed')
            "
        );

        $statement->execute([
            'id' => $appointmentId,
            'doctor_id' => $doctorId,
            'room_id' => $roomId,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'notes' => $notes,
        ]);

        return $statement->rowCount() === 1;
    }

    
    public function changeStatus(
        int $id,
        string $newStatus
    ): bool {
        $statement = $this->pdo->prepare(
            "
            SELECT status
            FROM appointments
            WHERE id = :id
            LIMIT 1
            "
        );

        $statement->execute([
            'id' => $id,
        ]);

        $appointment = $statement->fetch();

        if ($appointment === false) {
            return false;
        }

        $currentStatus = $appointment['status'];

        $allowedTransitions = [
            'requested' => [
                'confirmed',
                'cancelled',
            ],

            'confirmed' => [
                'attended',
                'no_show',
                'cancelled',
            ],

            'attended' => [],
            'no_show' => [],
            'cancelled' => [],
        ];

        if (
            !isset($allowedTransitions[$currentStatus])
            || !in_array(
                $newStatus,
                $allowedTransitions[$currentStatus],
                true
            )
        ) {
            return false;
        }

        $update = $this->pdo->prepare(
            "
            UPDATE appointments
            SET status = :status
            WHERE id = :id
            "
        );

        $update->execute([
            'id' => $id,
            'status' => $newStatus,
        ]);

        return $update->rowCount() === 1;
    }
    /**
 * Obtener historial de citas de un paciente.
 */
public function getPatientHistory(int $patientId): array
{
    $sql = "
        SELECT
            a.id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.notes,

            d.first_name AS doctor_first_name,
            d.last_name AS doctor_last_name,
            d.specialty,

            r.code AS room_code,
            r.name AS room_name

        FROM appointments a

        INNER JOIN doctors d
            ON d.id = a.doctor_id

        INNER JOIN rooms r
            ON r.id = a.room_id

        WHERE a.patient_id = :patient_id

        ORDER BY
            a.appointment_date ASC,
            a.appointment_time ASC
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        ':patient_id' => $patientId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}   