
<?php

$term = $term ?? '';
$patients = $patients ?? [];
$page = $page ?? 1;
$perPage = $perPage ?? 10;
$total = $total ?? 0;
$totalPages = $totalPages ?? 1;

$baseUrl = '/patients';

?>

<section class="page-header">

    <div>
        <h1>Pacientes</h1>
        <p>Listado de los pacientes registrados</p>
    </div>

    <a
        class="button primary"
        href="<?= e(url('/patients/create')) ?>"
    >
        Nuevo paciente
    </a>

</section>


<!-- Buscador -->
<form
    class="search-form"
    method="get"
    action="<?= e(url('/patients')) ?>"
>

    <label
        class="sr-only"
        for="q"
    >
        Término de búsqueda
    </label>

    <input
        type="text"
        id="q"
        name="q"
        value="<?= e($term) ?>"
        placeholder="Documento o nombre"
    >

    <label
        class="sr-only"
        for="per_page"
    >
        Registros por página
    </label>

    <select
        id="per_page"
        name="per_page"
    >

        <?php foreach ([5, 10, 25, 50] as $option): ?>

            <option
                value="<?= e((string) $option) ?>"
                <?= $option === $perPage ? 'selected' : '' ?>
            >
                <?= e((string) $option) ?> por página
            </option>

        <?php endforeach; ?>

    </select>


    <button
        class="button secondary"
        type="submit"
    >
        Buscar
    </button>


    <?php if ($term !== ''): ?>

        <a
            class="button secondary"
            href="<?= e(url('/patients')) ?>"
        >
            Limpiar
        </a>

    <?php endif; ?>

</form>


<!-- Tabla de pacientes -->

<?php if ($patients === []): ?>

    <section class="empty-state">

        <p>
            No se encontraron pacientes para los filtros aplicados.
        </p>

    </section>

<?php else: ?>

    <div class="table-wrap">

        <table>

            <thead>

                <tr>
                    <th>Documento</th>
                    <th>Paciente</th>
                    <th>Nacimiento</th>
                    <th>Contacto</th>
                    <th>Acciones</th>
                </tr>

            </thead>


            <tbody>

                <?php foreach ($patients as $patient): ?>

                    <tr>

                        <!-- Documento -->
                        <td>
                            <?= e($patient['document_type']) ?>
                            <?= e($patient['document_number']) ?>
                        </td>


                        <!-- Nombre -->
                        <td>
                            <?= e(
                                $patient['first_name']
                                . ' '
                                . $patient['last_name']
                            ) ?>
                        </td>


                        <!-- Fecha de nacimiento -->
                        <td>
                            <?= e(
                                format_date(
                                    $patient['birth_date']
                                )
                            ) ?>
                        </td>


                        <!-- Contacto -->
                        <td>
                            <?= e(
                                $patient['phone']
                                ?: $patient['email']
                                ?: 'Sin dato'
                            ) ?>
                        </td>


                        <!-- Acciones -->
                        <td class="actions">

                            <!-- Editar -->
                            <a
                                class="button secondary"
                                href="<?= e(
                                    url(
                                        '/patients/'
                                        . $patient['id']
                                        . '/edit'
                                    )
                                ) ?>"
                            >
                                Editar
                            </a>


                            <!-- Eliminar -->
                            <form
                                method="POST"
                                action="<?= e(
                                    url(
                                        '/patients/'
                                        . $patient['id']
                                        . '/delete'
                                    )
                                ) ?>"
                                style="display: inline;"
                                onsubmit="return confirm(
                                    '¿Está seguro de eliminar este paciente?'
                                );"
                            >

                                <?= csrf_field() ?>

                                <button
                                    type="submit"
                                    class="button danger"
                                >
                                    Eliminar
                                </button>

                            </form>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>


    <!-- Paginación -->

    <?php
    require __DIR__ . '/../partials/pagination.php';
    ?>

<?php endif; ?>