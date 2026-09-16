<section class="page-header">
    <div>
        <h1>Registrar Medico</h1>
        <p>Complete los datos obligatorios.</p>
    </div>
</section>

<form
    class="panel form-grid"
    method="post"
    action="<?= e(url('/doctors')) ?>"
>
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="license_number">Número de licencia</label>

        <input
            type="text"
            id="license_number"
            name="license_number"
            value="<?= e($data['license_number'] ?? '') ?>"
            required
        >
    </div>

    <div class="form-group">
        <label for="first_name">Nombres</label>

        <input
            type="text"
            id="first_name"
            name="first_name"
            value="<?= e($data['first_name'] ?? '') ?>"
            required
        >
    </div>

    <div class="form-group">
        <label for="last_name">Apellidos</label>

        <input
            type="text"
            id="last_name"
            name="last_name"
            value="<?= e($data['last_name'] ?? '') ?>"
            required
        >
    </div>

    <div class="form-group">
        <label for="specialty">Especialidad</label>

        <select
            id="specialty"
            name="specialty"
            required
        >
            <option value="">Seleccione</option>

            <option value="Odontología general">
                Odontología general
            </option>

            <option value="Ortodoncia">
                Ortodoncia
            </option>

            <option value="Endodoncia">
                Endodoncia
            </option>
        </select>
    </div>

    <div class="form-group">
        <label for="active">Estado</label>

        <select
            id="active"
            name="active"
            required
        
        >
         <option value="">Seleccione</option>
            <option value="active">
                Activo
            </option>
            <option value="active">
                Inactivo
            </option>
        </select>
    </div>

    <div class="form-actions">
        <a
            class="button secondary"
            href="<?= e(url('/doctors')) ?>"
        >
            Cancelar
        </a>

        <button
            type="submit"
            class="button primary"
        >
            Guardar médico
        </button>
    </div>
</form>