<?php
$data = $data ?? [];
?>

<form
    class="panel form-grid"
    method="post"
    action="<?= e(url('/doctors/' . ($data['id'] ?? ''))) ?>"
    novalidate
>

    <?= csrf_field() ?>

    <!-- Número de licencia -->
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

    <!-- Especialidad -->
    <div class="form-group">
        <label for="specialty">Especialidad</label>

        <select id="specialty" name="specialty" required>
            <option value="">Seleccione</option>

            <option value="g"
                <?= (($data['specialty'] ?? '') === 'g') ? 'selected' : '' ?>>
                Odontología general
            </option>

            <option value="o"
                <?= (($data['specialty'] ?? '') === 'o') ? 'selected' : '' ?>>
                Ortodoncia
            </option>

            <option value="e"
                <?= (($data['specialty'] ?? '') === 'e') ? 'selected' : '' ?>>
                Endodoncia
            </option>
        </select>
    </div>

    <!-- Estado -->
    <div class="form-group">
        <label for="active">Estado</label>

        <select id="active" name="active" required>
            <option value="">Seleccione</option>

            <option value="a"
                <?= (($data['active'] ?? '') === 'a') ? 'selected' : '' ?>>
                Activo
            </option>

            <option value="i"
                <?= (($data['active'] ?? '') === 'i') ? 'selected' : '' ?>>
                Inactivo
            </option>
        </select>
    </div>

    <!-- Botones -->
    <div class="form-actions">

        <a
            class="button secondary" href="<?= e(url('/doctors')) ?>">
            Cancelar
        </a>

        <button class="button primary" type="submit">
            Actualizar médico
        </button>

    </div>

</form>