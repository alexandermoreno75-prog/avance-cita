<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repository\DoctorRepository;
use PDOException;

final class DoctorController
{
    public function __construct(
        private DoctorRepository $doctors
    ) {
    }

    /**
     * Mostrar médicos registrados.
     */
    public function index(): void
    {
        Auth::requireLogin();

        $term = trim((string) ($_GET['q'] ?? ''));

        View::render('doctors/index', [
            'title' => 'Médicos',
            'doctors' => $this->doctors->search($term),
            'term' => $term,
        ]);
    }

    /**
     * Mostrar formulario para registrar médico.
     */
    public function create(): void
    {
        Auth::requireLogin();

        View::render('doctors/create', [
            'title' => 'Registrar médico',
            'data' => [],
            'errors' => [],
        ]);
    }

    /**
     * Registrar un nuevo médico.
     */
    public function store(): void
    {
        Auth::requireLogin();

        Csrf::requireValid($_POST['_token'] ?? null);

        $data = [
            'license_number' => strtoupper(
                trim((string) ($_POST['license_number'] ?? ''))
            ),

            'first_name' => trim(
                (string) ($_POST['first_name'] ?? '')
            ),

            'last_name' => trim(
                (string) ($_POST['last_name'] ?? '')
            ),

            'specialty' => trim(
                (string) ($_POST['specialty'] ?? '')
            ),

            // 1 = médico activo
            'active' => 1,
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('doctors/create', [
                'title' => 'Registrar médico',
                'data' => $data,
                'errors' => $errors,
            ]);

            return;
        }

        try {
            $this->doctors->create($data);
        } catch (PDOException $exception) {

            if ($exception->getCode() === '23000') {
                $errors['license_number'] =
                    'Ya existe un médico con ese número de licencia.';

                View::render('doctors/create', [
                    'title' => 'Registrar médico',
                    'data' => $data,
                    'errors' => $errors,
                ]);

                return;
            }

            throw $exception;
        }

        flash(
            'success',
            'Médico registrado correctamente.'
        );

        redirect('/doctors');
    }

    /**
     * Mostrar formulario para editar un médico.
     */
    public function edit(int $id): void
    {
        Auth::requireLogin();

        $doctor = $this->doctors->findById($id);

        if ($doctor === null) {
            http_response_code(404);
            echo 'Médico no encontrado.';
            return;
        }

        View::render('doctors/edit', [
            'title' => 'Editar médico',
            'data' => $doctor,
            'errors' => [],
        ]);
    }

    /**
     * Actualizar los datos de un médico.
     */
    public function update(int $id): void
    {
        Auth::requireLogin();

        Csrf::requireValid($_POST['_token'] ?? null);

        $doctor = $this->doctors->findById($id);

        if ($doctor === null) {
            http_response_code(404);
            echo 'Médico no encontrado.';
            return;
        }

        $data = [
            'license_number' => strtoupper(
                trim((string) ($_POST['license_number'] ?? ''))
            ),

            'first_name' => trim(
                (string) ($_POST['first_name'] ?? '')
            ),

            'last_name' => trim(
                (string) ($_POST['last_name'] ?? '')
            ),

            'specialty' => trim(
                (string) ($_POST['specialty'] ?? '')
            ),

            'active' => isset($_POST['active'])
                ? (int) $_POST['active']
                : 1,
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('doctors/edit', [
                'title' => 'Editar médico',
                'data' => $data,
                'errors' => $errors,
            ]);

            return;
        }

        try {
            $this->doctors->update($id, $data);
        } catch (PDOException $exception) {

            if ($exception->getCode() === '23000') {
                $errors['license_number'] =
                    'Ya existe otro médico con esa licencia.';

                View::render('doctors/edit', [
                    'title' => 'Editar médico',
                    'data' => $data,
                    'errors' => $errors,
                ]);

                return;
            }

            throw $exception;
        }

        flash('success','Médico actualizado correctamente.');

        redirect('/doctors');
    }

  private function validate(array $data): array
    {
        $errors = [];

        // Validar licencia
        if (
            !preg_match(
                '/^[A-Z0-9-]{3,30}$/',
                $data['license_number']
            )
        ) {
            $errors['license_number'] =
                'La licencia debe tener entre 3 y 30 letras, números o guiones.';
        }

        // Validar nombres
        if (
            mb_strlen($data['first_name']) < 2 ||
            mb_strlen($data['first_name']) > 80
        ) {
            $errors['first_name'] =
                'Ingrese nombres de 2 a 80 caracteres.';
        }

        // Validar apellidos
        if (
            mb_strlen($data['last_name']) < 2 ||
            mb_strlen($data['last_name']) > 80
        ) {
            $errors['last_name'] =
                'Ingrese apellidos de 2 a 80 caracteres.';
        }

        // Especialidades permitidas
        $validSpecialties = [
            'Odontología general',
            'Ortodoncia',
            'Endodoncia',
        ];

        if (
            !in_array(
                $data['specialty'],
                $validSpecialties,
                true
            )
        ) {
            $errors['specialty'] =
                'Seleccione una especialidad válida.';
        }

        // Validar estado
        if (
            !in_array(
                $data['active'],
                [0, 1],
                true
            )
        ) {
            $errors['active'] =
                'El estado del médico no es válido.';
        }

        return $errors;
    }

    /**
     * Eliminar un médico.
     */
   public function delete(int $id): void
{
    Auth::requireLogin();

    Csrf::requireValid($_POST['_token'] ?? null);

    $doctor = $this->doctors->findById($id);

    if ($doctor === null) {
        http_response_code(404);
        echo 'medico no encontrado.';
        return;
    }

    try {
        $this->doctors->delete($id);

        flash(
            'success',
            'medico eliminado correctamente.'
        );

        redirect('/doctors');
    } catch (PDOException $exception) {

        if ($exception->getCode() === '23000') {
            flash(
                'error',
                'No se puede eliminar el medico porque tiene información relacionada.'
            );

            redirect('/doctors');
            return;
        }

        throw $exception;
    }
}
}