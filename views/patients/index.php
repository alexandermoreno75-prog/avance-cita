<section class="page-header">
    <div>
        <h1>Pacientes</h1>
        <p>Busque por documento, nombres o apellidos.</p>
    </div>

    <a class="button primary" href="<?= e(url('/patients/create')) ?>">
        Nuevo paciente
    </a>
</section>

<form class="search-form" method="get" action="<?= e(url('/patients')) ?>">
    <label class="sr-only" for="q">Término de búsqueda</label>

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
                <th>Documento</th>
                <th>Paciente</th>
                <th>Nacimiento</th>
                <th>Contacto</th>
                <th>Opciones</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td>
                        <?= e($patient['document_type']) ?>
                        <?= e($patient['document_number']) ?>
                    </td>

                    <td>
                        <?= e($patient['first_name'] . ' ' . $patient['last_name']) ?>
                    </td>

                    <td>
                        <?= e(format_date($patient['birth_date'])) ?>
                    </td>

                    <td>
                        <?= e(
                            $patient['phone']
                                ?: $patient['email']
                                ?: 'Sin dato'
                        ) ?>
                    </td>

                    <td>
                        <a
                            class="button secondary"
                            href="<?= e(url('/patients/' . $patient['id'] . '/edit')) ?>"
                        >
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($patients === []): ?>
                <tr>
                    <td colspan="5">
                        No se encontraron pacientes.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
