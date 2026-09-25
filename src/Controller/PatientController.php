<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repository\PatientRepository;
use App\Repository\AppointmentRepository;
use DateTimeImmutable;
use PDOException;

final class PatientController
{


    public function __construct(
        private PatientRepository $patients,
        private AppointmentRepository $appointments
    ) {
    }

    public function index(): void
    {
        Auth::requireLogin();

        $term = trim((string) ($_GET['q'] ?? ''));

        if (mb_strlen($term) > 100) {
            $term = mb_substr($term, 0, 100);
        }

        $page = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT
        );

        if ($page === false || $page === null || $page < 1) {
            $page = 1;
        }

        $perPage = filter_input(
            INPUT_GET,
            'per_page',
            FILTER_VALIDATE_INT
        );

        if ($perPage === false || $perPage === null) {
            $perPage = 10;
        }

        $perPage = max(5, min(50, $perPage));

        $result = $this->patients->paginate(
            $term,
            $page,
            $perPage
        );

        $totalPages = max(
            1,
            (int) ceil($result['total'] / $perPage)
        );

        if ($page > $totalPages) {
            $query = http_build_query(
                array_filter([
                    'q' => $term,
                    'per_page' => $perPage === 10 ? null : $perPage,
                    'page' => $totalPages,
                ])
            );

            redirect(
                '/patients' .
                ($query !== '' ? '?' . $query : '')
            );

            return;
        }

        View::render(
            'patients/index',
            [
                'title' => 'Pacientes',
                'patients' => $result['items'],
                'term' => $term,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $result['total'],
                'totalPages' => $totalPages,
            ]
        );
    }

    public function create(): void
    {
        Auth::requireLogin();

        View::render('patients/create', [
            'title' => 'Registrar paciente',
            'data' => [],
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();

        Csrf::requireValid($_POST['_token'] ?? null);

        $data = [
            'document_type' => strtoupper(
                trim((string) ($_POST['document_type'] ?? 'CC'))
            ),

            'document_number' => strtoupper(
                trim((string) ($_POST['document_number'] ?? ''))
            ),

            'first_name' => trim(
                (string) ($_POST['first_name'] ?? '')
            ),

            'last_name' => trim(
                (string) ($_POST['last_name'] ?? '')
            ),

            'birth_date' => trim(
                (string) ($_POST['birth_date'] ?? '')
            ),

            'sex' => strtoupper(
                trim((string) ($_POST['sex'] ?? ''))
            ),

            'phone' => trim(
                (string) ($_POST['phone'] ?? '')
            ) ?: null,

            'email' => mb_strtolower(
                trim((string) ($_POST['email'] ?? ''))
            ) ?: null,
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render(
                'patients/create',
                compact('data', 'errors') + [
                    'title' => 'Registrar paciente',
                ]
            );

            return;
        }

        try {
            $this->patients->create($data);
        } catch (PDOException $exception) {

            if ($exception->getCode() === '23000') {
                $errors['document_number'] =
                    'Ya existe un paciente con ese documento.';

                View::render(
                    'patients/create',
                    compact('data', 'errors') + [
                        'title' => 'Registrar paciente',
                    ]
                );

                return;
            }

            throw $exception;
        }

        flash(
            'success',
            'Paciente registrado correctamente.'
        );

        redirect('/patients');
    }

    private function validate(array $data): array
    {
        $errors = [];

        // Tipo de documento
        if (!in_array(
            $data['document_type'],
            ['CC', 'TI', 'CE', 'PA'],
            true
        )) {
            $errors['document_type'] =
                'Seleccione un tipo de documento válido.';
        }

        // Número de documento
        if (!preg_match(
            '/^[A-Z0-9-]{5,30}$/',
            $data['document_number']
        )) {
            $errors['document_number'] =
                'El documento debe tener entre 5 y 30 letras, números o guiones.';
        }

        // Nombres
        if (
            mb_strlen($data['first_name']) < 2 ||
            mb_strlen($data['first_name']) > 80
        ) {
            $errors['first_name'] =
                'Ingrese nombres de 2 a 80 caracteres.';
        }

        // Apellidos
        if (
            mb_strlen($data['last_name']) < 2 ||
            mb_strlen($data['last_name']) > 80
        ) {
            $errors['last_name'] =
                'Ingrese apellidos de 2 a 80 caracteres.';
        }

        // Fecha de nacimiento
        if ($data['birth_date'] === '') {

            $errors['birth_date'] =
                'Ingrese la fecha de nacimiento.';

        } else {

            $birth = DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $data['birth_date']
            );

            $dateErrors = DateTimeImmutable::getLastErrors();

            if (
                $birth === false ||
                (
                    is_array($dateErrors) &&
                    (
                        $dateErrors['warning_count'] > 0 ||
                        $dateErrors['error_count'] > 0
                    )
                )
            ) {
                $errors['birth_date'] =
                    'Ingrese una fecha de nacimiento válida.';

            } elseif ($birth > new DateTimeImmutable('today')) {

                $errors['birth_date'] =
                    'La fecha de nacimiento no puede ser futura.';
            }
        }

        // Sexo
        if (!in_array(
            $data['sex'],
            ['M', 'F', 'O'],
            true
        )) {
            $errors['sex'] =
                'Seleccione un sexo válido.';
        }

        // Teléfono
        if (
            $data['phone'] !== null &&
            !preg_match(
                '/^[0-9+\-\s()]{7,20}$/',
                $data['phone']
            )
        ) {
            $errors['phone'] =
                'Ingrese un número de teléfono válido.';
        }

        // Correo electrónico
        if (
            $data['email'] !== null &&
            !filter_var(
                $data['email'],
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors['email'] =
                'Ingrese un correo electrónico válido.';
        }

        return $errors;
    }
}

