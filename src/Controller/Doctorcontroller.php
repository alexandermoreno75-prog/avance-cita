<?php
declare(strict_types=1);
namespace App\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Repository\DoctorRepository;
use DateTimeImmutable;
use PDOException;
final class DoctorController
{
    public function __construct(private DoctorRepository $doctors)
    {
    }
    public function index(): void
    {
        Auth::requireLogin();
        $term = trim((string) ($_GET['q'] ?? ''));
        View::render('doctors/index', [
            'title' => 'Medicos',
            'doctors' => $this->doctors->search($term),
            'term' => $term
        ]);
    }
    public function create(): void
    {
        Auth::requireLogin();
        View::render('doctors/create', [
            'title' => 'Registrar medico',
            'data' => [],
            'errors' => [],
        ]);
    }
    public function store(): void
    {
        Auth::requireLogin();
        Csrf::requireValid($_POST['_token'] ?? null);

        $data = [
            'license_number' => strtoupper(trim((string) ($_POST['license_number'] ?? ''))),
            'first_name'     => trim((string) ($_POST['first_name'] ?? '')),
            'last_name'      => trim((string) ($_POST['last_name'] ?? '')),
            'specialty'      => trim((string) ($_POST['specialty'] ?? '')),
            'active'         => (string) ($_POST['active'] ?? '1') === '1' ? 1 : 0,
        ];

        $errors = $this->validate($data);

        if ($errors !== []) {
            View::render('doctors/create', compact('data', 'errors') + ['title' => 'Registrar médico']);
            return;
        }

        try {
            $this->doctors->create($data);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors['license_number'] = 'Ya existe un médico con ese número de licencia.';
                View::render('doctors/create', compact('data', 'errors') + ['title' => 'Registrar médico']);
                return;
            }
            throw $exception;
        }

        flash('success', 'Médico registrado correctamente.');
        redirect('/doctors');
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (!preg_match('/^[A-Z0-9-]{3,30}$/', $data['license_number'])) {
            $errors['license_number'] = 'La licencia debe tener entre 3 y 30 letras, números o guiones.';
        }

        if (mb_strlen($data['first_name']) < 2 || mb_strlen($data['first_name']) > 80) {
            $errors['first_name'] = 'Ingrese nombres de 2 a 80 caracteres.';
        }

        if (mb_strlen($data['last_name']) < 2 || mb_strlen($data['last_name']) > 80) {
            $errors['last_name'] = 'Ingrese apellidos de 2 a 80 caracteres.';
        }

        $validSpecialties = ['Odontología general', 'Ortodoncia', 'Endodoncia'];
        if (!in_array($data['specialty'], $validSpecialties, true)) {
            $errors['specialty'] = 'Seleccione una especialidad válida.';
        }

        if (!in_array($data['active'], [0, 1], true)) { 
            $errors['active'] = 'Seleccione un estado válido.';
        }
         return $errors;
    }
    
}
