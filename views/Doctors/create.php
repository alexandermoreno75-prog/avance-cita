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
    novalidate
>
    <?= csrf_field() ?>

    
<div>
    <label for="license_number">Número de licencia</label>

    <input
        type="text"
        id="license_number"
        name="license_number"
        maxlength="30"
        value="<?= e($data['license_number'] ?? '') ?>"
        required
    >

    <?php if (isset($errors['license_number'])): ?>
        <small class="field-error">
            <?= e($errors['license_number']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Nombres -->
<div>
    <label for="first_name">Nombres</label>

    <input
        type="text"
        id="first_name"
        name="first_name"
        maxlength="80"
        value="<?= e($data['first_name'] ?? '') ?>"
        required
    >

    <?php if (isset($errors['first_name'])): ?>
        <small class="field-error">
            <?= e($errors['first_name']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Apellidos -->
<div>
    <label for="last_name">Apellidos</label>

    <input
        type="text"
        id="last_name"
        name="last_name"
        maxlength="80"
        value="<?= e($data['last_name'] ?? '') ?>"
        required
    >

    <?php if (isset($errors['last_name'])): ?>
        <small class="field-error">
            <?= e($errors['last_name']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Especialidad -->
<div>
    <label for="specialty">Especialidad</label>

    <select
        id="ortodoncia general"
        name="ortodoncia"
        name="endodoncia"
        required
    >
        <option value="">Seleccione</option>

        <option
            value="g"
            <?= ($data['ortodoncia general'] ?? '') === 'g' ? 'selected' : '' ?>
        >
            ortodocia general
        </option>
        <option
            value="g"
            <?= ($data['ortodoncia'] ?? '') === 'g' ? 'selected' : '' ?>
        >
            ortodocia 
        </option>

        <option
            value="o"
            <?= ($data['specialty'] ?? '') === 'I' ? 'selected' : '' ?>
        >
            endodoncia
        </option>
    </select>

</div>

<!-- Estado -->
<div>
    <label for="active">Estado</label>

    <select
        id="active"
        name="active"
        required
    >
        <option value="">Seleccione</option>

        <option
            value="A"
            <?= ($data['active'] ?? '') === 'A' ? 'selected' : '' ?>
        >
            Activo
        </option>

        <option
            value="I"
            <?= ($data['active'] ?? '') === 'I' ? 'selected' : '' ?>
        >
            Inactivo
        </option>
    </select>

    <?php if (isset($errors['active'])): ?>
        <small class="field-error">
            <?= e($errors['active']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Acciones -->
<div class="form-actions full-width">
    <a
        class="button secondary"
        href="<?= e(url('/doctors')) ?>"
    >
        Cancelar
    </a>

    <button
        class="button primary"
        type="submit"
    >
        Guardar médico
    </button>
</div>
