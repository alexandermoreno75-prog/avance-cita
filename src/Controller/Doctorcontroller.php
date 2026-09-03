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
 'title' => 'Pacientes'//,
 //'patients' => $this->patients->search($term),
 //'term' => $term,
 ]);
 }
 public function create(): void
{
    Auth::requireLogin();

    View::render('doctors/create', [
        'title' => 'Crear Médico',
    ]);
}
}
