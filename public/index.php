<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Cargar Composer
|--------------------------------------------------------------------------
*/

require_once $projectRoot . '/vendor/autoload.php';


/*
|--------------------------------------------------------------------------
| Imports
|--------------------------------------------------------------------------
*/

use App\Controller\ApiController;
use App\Controller\AppointmentController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\DoctorController;
use App\Controller\HealthController;
use App\Controller\PatientController;
use App\Controller\PasswordController;

use App\Core\Database;
use App\Core\ErrorHandler;
use App\Core\Router;

use App\Domain\SlotGenerator;

use App\Repository\AppointmentRepository;
use App\Repository\DoctorRepository;
use App\Repository\PatientRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;

use App\Service\AppointmentService;


/*
|--------------------------------------------------------------------------
| Cargar ErrorHandler
|--------------------------------------------------------------------------
*/

$errorHandlerFile = $projectRoot . '/src/Core/ErrorHandler.php';

if (!is_file($errorHandlerFile)) {
    die(
        'ERROR: No se encuentra el archivo: '
        . $errorHandlerFile
    );
}

require_once $errorHandlerFile;


/*
|--------------------------------------------------------------------------
| Registrar manejo de errores
|--------------------------------------------------------------------------
*/

ErrorHandler::register();


/*
|--------------------------------------------------------------------------
| Configuración
|--------------------------------------------------------------------------
*/

$config = require $projectRoot . '/bootstrap/app.php';


/*
|--------------------------------------------------------------------------
| Cabeceras de seguridad
|--------------------------------------------------------------------------
*/

header('X-Content-Type-Options: nosniff');

header('X-Frame-Options: DENY');

header('Referrer-Policy: strict-origin-when-cross-origin');

header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self'; "
    . "style-src 'self'; "
    . "img-src 'self' data:; "
    . "base-uri 'self'; "
    . "frame-ancestors 'none'; "
    . "form-action 'self'"
);


/*
|--------------------------------------------------------------------------
| Aplicación
|--------------------------------------------------------------------------
*/

try {

    /*
    |--------------------------------------------------------------------------
    | Base de datos
    |--------------------------------------------------------------------------
    */

    $database = new Database(
        $config['database']
    );

    $pdo = $database->pdo();


    /*
    |--------------------------------------------------------------------------
    | Repositorios
    |--------------------------------------------------------------------------
    */

    $users = new UserRepository($pdo);

    $patients = new PatientRepository($pdo);

    $doctors = new DoctorRepository($pdo);

    $rooms = new RoomRepository($pdo);

    $appointments = new AppointmentRepository($pdo);


    /*
    |--------------------------------------------------------------------------
    | Servicio de citas
    |--------------------------------------------------------------------------
    */

    $appointmentService = new AppointmentService(
        $database,
        $appointments,
        $doctors,
        $rooms,
        new SlotGenerator(),
        $config['appointments']
    );


    /*
    |--------------------------------------------------------------------------
    | Controladores
    |--------------------------------------------------------------------------
    */

    $authController = new AuthController(
        $users
    );

    $dashboardController = new DashboardController(
        $patients,
        $appointments
    );

    $doctorsController = new DoctorController(
        $doctors
    );

    $patientController = new PatientController(
        $patients,
        $appointments
    );

    $appointmentController = new AppointmentController(
        $patients,
        $doctors,
        $rooms,
        $appointments,
        $appointmentService
    );

    $apiController = new ApiController(
        $appointmentService
    );

    $healthController = new HealthController(
        $pdo
    );

    $passwordController = new PasswordController(
        $users
    );


    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    */

    $router = new Router(
        $config['base_path']
    );


    /*
    |--------------------------------------------------------------------------
    | Health check
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/health',
        [$healthController, 'show']
    );


    /*
    |--------------------------------------------------------------------------
    | Autenticación
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/login',
        [$authController, 'showLogin']
    );

    $router->post(
        '/login',
        [$authController, 'login']
    );

    $router->post(
        '/logout',
        [$authController, 'logout']
    );


    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/',
        [$dashboardController, 'index']
    );


    /*
    |--------------------------------------------------------------------------
    | Médicos
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/doctors',
        [$doctorsController, 'index']
    );

    $router->get(
        '/doctors/create',
        [$doctorsController, 'create']
    );

    $router->post(
        '/doctors',
        [$doctorsController, 'store']
    );

    $router->get(
        '/doctors/{id}/edit',
        [$doctorsController, 'edit']
    );

    $router->post(
        '/doctors/{id}',
        [$doctorsController, 'update']
    );

    $router->post(
        '/doctors/{id}/delete',
        [$doctorsController, 'delete']
    );


    /*
    |--------------------------------------------------------------------------
    | Pacientes
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/patients',
        [$patientController, 'index']
    );

    $router->get(
        '/patients/create',
        [$patientController, 'create']
    );

    $router->post(
        '/patients',
        [$patientController, 'store']
    );

    $router->get(
        '/patients/{id}/edit',
        [$patientController, 'edit']
    );

    $router->post(
        '/patients/{id}',
        [$patientController, 'update']
    );

    $router->post(
        '/patients/{id}/delete',
        [$patientController, 'delete']
    );


    /*
    |--------------------------------------------------------------------------
    | Citas
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/appointments',
        [$appointmentController, 'index']
    );

    $router->get(
        '/appointments/create',
        [$appointmentController, 'create']
    );

    $router->post(
        '/appointments',
        [$appointmentController, 'store']
    );

    $router->get(
        '/appointments/{id}',
        [$appointmentController, 'show']
    );

    $router->post(
        '/appointments/{id}/cancel',
        [$appointmentController, 'cancel']
    );

    $router->post(
        '/appointments/{id}/complete',
        [$appointmentController, 'complete']
    );


    /*
    |--------------------------------------------------------------------------
    | Cambio de contraseña
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/change-password',
        [$passwordController, 'edit']
    );

    $router->post(
        '/change-password',
        [$passwordController, 'update']
    );


    /*
    |--------------------------------------------------------------------------
    | API
    |--------------------------------------------------------------------------
    */

    $router->get(
        '/api/availability',
        [$apiController, 'availability']
    );


    /*
    |--------------------------------------------------------------------------
    | Ejecutar Router
    |--------------------------------------------------------------------------
    */

    $router->dispatch(
        $_SERVER['REQUEST_METHOD'] ?? 'GET',
        $_SERVER['REQUEST_URI'] ?? '/'
    );

} catch (\Throwable $exception) {

    /*
    |--------------------------------------------------------------------------
    | Error 500
    |--------------------------------------------------------------------------
    */

    error_log(
        '[' . date('Y-m-d H:i:s') . '] '
        . $exception->getMessage()
        . ' en '
        . $exception->getFile()
        . ':'
        . $exception->getLine()
    );

    http_response_code(500);

    /*
    | No mostrar SQLSTATE, stack trace ni información técnica.
    */

    $errorPage = $projectRoot . '/views/errors/500.php';

    if (is_file($errorPage)) {
        require $errorPage;
    } else {
        echo '<h1>Error 500</h1>';
        echo '<p>Ocurrió un error interno. Intenta nuevamente.</p>';
    }

    exit;
}
