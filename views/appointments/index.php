<section class="page-header">
    <div>
        <h1>Citas</h1>
        <p>Consulte el historial o filtre por documento del paciente.</p>
    </div>

    <a class="button primary" href="<?= e(url('/appointments/create')) ?>">
        Asignar cita
    </a>
</section>

<form class="search-form" method="get" action="<?= e(url('/appointments')) ?>">
    <label class="sr-only" for="document">Documento</label>

    <input
        id="document"
        name="document"
        value="<?= e($document) ?>"
        placeholder="Documento del paciente"
    >

    <button class="button secondary" type="submit">
        Consultar
    </button>

    <?php if ($document !== ''): ?>
        <a class="button secondary" href="<?= e(url('/appointments')) ?>">
            Limpiar
        </a>
    <?php endif; ?>
</form>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Fecha y hora</th>
                <th>Paciente</th>
                <th>Médico</th>
                <th>Consultorio</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($appointments as $appointment): ?>
                <tr>
                    <td>
                        <a href="<?= e(url('/appointments/' . $appointment['id'])) ?>">
                            #<?= e($appointment['id']) ?>
                        </a>
                    </td>

                    <td>
                        <?= e(format_date($appointment['appointment_date'])) ?>
                        <?= e(format_time($appointment['appointment_time'])) ?>
                    </td>

                    <td>
                        <?= e(
                            $appointment['patient_first_name'] . ' ' .
                            $appointment['patient_last_name']
                        ) ?>
                        <br>
                        <small>
                            <?= e($appointment['document_number']) ?>
                        </small>
                    </td>

                    <td>
                        <?= e(
                            $appointment['doctor_first_name'] . ' ' .
                            $appointment['doctor_last_name']
                        ) ?>
                    </td>

                    <td>
                        <?= e($appointment['room_code']) ?>
                    </td>

                    <td>
                        <span class="status <?= e($appointment['status']) ?>">
                            <?= e(appointment_status_label($appointment['status'])) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if ($appointments === []): ?>
                <tr>
                    <td colspan="6">No hay citas para mostrar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
