<?php
$term = $term ?? '';
$doctors = $doctors ?? [];
?>

<section class="page-header">
    <div>
        <h1>Médicos</h1>
        <p>Listado de médicos registrados</p>
    </div>

    <a class="button primary" href="<?= e(url('/doctors/create')) ?>">
        Nuevo Médico
    </a>

</section>

<form class="search-form" method="get" action="<?= e(url('/doctor')) ?>">
    <label class="sr-only" for="q">
        Término de búsqueda
    </label>

    <input
        id="q"
        name="q"
        value="<?= e($term) ?>"
        placeholder="Documento o nombre"
    >

    <button class="button secondary" type="submit">
        Buscar
    </button>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Número de licencia</th>
                <th>Médico</th>
                <th>Especialidad</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?= e($doctor['license_number']) ?></td>

                    <td>
                        <?= e($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                    </td>

                    <td><?= e($doctor['specialty']) ?></td>

                    <td>
                        <?php if ($doctor['active']): ?>
                            <span class="status active">Activo</span>
                        <?php else: ?>
                            <span class="status inactive">Inactivo</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($doctors === []): ?>
                <tr>
                    <td colspan="4">
                        No se encontraron médicos.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
