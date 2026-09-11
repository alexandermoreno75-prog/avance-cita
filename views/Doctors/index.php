<?php

$term = $term ?? '';
$doctors = $doctors ?? [];

?>

<section class="page-header">
    <div>
        <h1>Médicos</h1>
        <p>Listado de médicos registrados</p>
    </div>

    <a
        class="button primary"
        href="<?= e(url('/doctors/create')) ?>"
    >
        Nuevo Médico
    </a>
</section>


<!-- Buscador -->
<form
    class="search-form"
    method="get"
    action="<?= e(url('/doctors')) ?>"
>
    <label class="sr-only" for="q">
        Término de búsqueda
    </label>

    <input
        id="q"
        name="q"
        type="text"
        value="<?= e($term) ?>"
        placeholder="Documento o nombre"
    >

    <button
        class="button secondary"
        type="submit"
    >
        Buscar
    </button>
</form>


<!-- Tabla de médicos -->
<div class="table-wrap">
    <table>

        <thead>
            <tr>
                <th>Número de licencia</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

            <?php if ($doctors !== []): ?>

                <?php foreach ($doctors as $doctor): ?>

                    <tr>

                        <!-- Número de licencia -->
                        <td>
                            <?= e($doctor['license_number'] ?? 'Sin dato') ?>
                        </td>


                        <!-- Nombre del médico -->
                        <td>
                            <?= e(
                                ($doctor['first_name'] ?? '') . ' ' .
                                ($doctor['last_name'] ?? '')
                            ) ?>
                        </td>


                        <!-- Especialidad -->
                        <td>
                            <?= e($doctor['specialty'] ?? 'Sin dato') ?>
                        </td>


                        <!-- Estado -->
                        <td>
                            <?php if (!empty($doctor['active'])): ?>

                                <span class="status active">
                                    Activo
                                </span>

                            <?php else: ?>

                                <span class="status inactive">
                                    Inactivo
                                </span>

                            <?php endif; ?>
                        </td>


                        <!-- Acciones -->
                        <td class="actions">

                            <!-- Editar -->
                            <a class="button secondary" href="<?= e(url('/doctors/' . $doctor['id'] . '/edit') ) ?>" >Editar</a>


                            <!-- Eliminar -->
                            <form method="POST" action="<?= e(url('/doctors/' . $doctor['id'] . '/delete')) ?>" style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar este médico?');" >

                                <?= csrf_field() ?>

                                <button type="submit" class="button danger">Eliminar</button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">
                        No se encontraron médicos.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>

    </table>
</div>