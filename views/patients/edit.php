<form
    class="panel form-grid"
    method="post"
    action="<?= e(url('/patients/' . ($data['id'] ?? ''))) ?>"
    novalidate
>

    <?= csrf_field() ?>

    <div class="form-group">
        <label for="document_type">Tipo de documento</label>

        <select id="document_type" name="document_type" required>
            <option value="CC"
                <?= (($data['document_type'] ?? '') === 'CC') ? 'selected' : '' ?>>
                Cédula de ciudadanía
            </option>

            <option value="TI"
                <?= (($data['document_type'] ?? '') === 'TI') ? 'selected' : '' ?>>
                Tarjeta de identidad
            </option>

            <option value="CE"
                <?= (($data['document_type'] ?? '') === 'CE') ? 'selected' : '' ?>>
                Cédula de extranjería
            </option>

            <option value="PA"
                <?= (($data['document_type'] ?? '') === 'PA') ? 'selected' : '' ?>>
                Pasaporte
            </option>
        </select>
    </div>


    <!-- Documento -->
    <div class="form-group">
        <label for="document_number">Documento</label>

        <input
            type="text"
            id="document_number"
            name="document_number"
            value="<?= e($data['document_number'] ?? '') ?>"
            required
        >
    </div>


    <!-- Nombres -->
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


    <!-- Apellidos -->
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


    <!-- Fecha de nacimiento -->
    <div class="form-group">
        <label for="birth_date">Fecha de nacimiento</label>

        <input
            type="date"
            id="birth_date"
            name="birth_date"
            value="<?= e($data['birth_date'] ?? '') ?>"
            required
        >
    </div>


    <!-- Sexo -->
    <div class="form-group">
        <label for="sex">Sexo</label>

        <select id="sex" name="sex" required>
            <option value="">Seleccione</option>

            <option value="F"
                <?= (($data['sex'] ?? '') === 'F') ? 'selected' : '' ?>>
                Femenino
            </option>

            <option value="M"
                <?= (($data['sex'] ?? '') === 'M') ? 'selected' : '' ?>>
                Masculino
            </option>

            <option value="O"
                <?= (($data['sex'] ?? '') === 'O') ? 'selected' : '' ?>>
                Otro / no informa
            </option>
        </select>
    </div>


    <!-- Teléfono -->
    <div class="form-group">
        <label for="phone">Teléfono</label>

        <input
            type="text"
            id="phone"
            name="phone"
            value="<?= e($data['phone'] ?? '') ?>"
        >
    </div>


    <!-- Correo -->
    <div class="form-group">
        <label for="email">Correo</label>

        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($data['email'] ?? '') ?>"
        >
    </div>


    <!-- Botones -->
    <div class="form-actions">

        <a
            class="button secondary"
            href="<?= e(url('/patients')) ?>"
        >
            Cancelar
        </a>

        <button
            class="button primary"
            type="submit"
        >
            Actualizar paciente
        </button>

    </div>

</form>