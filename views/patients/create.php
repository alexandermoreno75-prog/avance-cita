<section class="page-header">
    <div>
        <h1>Registrar paciente</h1>
        <p>Complete los datos obligatorios.</p>
    </div>
</section>

<form
    class="panel form-grid"
    method="post"
    action="<?= e(url('/patients')) ?>"
    novalidate
>
    <?= csrf_field() ?>


<!-- Tipo de documento -->
<div>
    <label for="document_type">Tipo de documento</label>

    <select id="document_type" name="document_type">
        <?php foreach (['CC', 'TI', 'CE', 'PA'] as $type): ?>
            <option
                value="<?= e($type) ?>"
                <?= ($data['document_type'] ?? 'CC') === $type ? 'selected' : '' ?>
            >
                <?= e($type) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if (isset($errors['document_type'])): ?>
        <small class="field-error">
            <?= e($errors['document_type']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Número de documento -->
<div>
    <label for="document_number">Número de documento</label>

    <input
        id="document_number"
        name="document_number"
        maxlength="30"
        value="<?= e($data['document_number'] ?? '') ?>"
        required
    >

    <?php if (isset($errors['document_number'])): ?>
        <small class="field-error">
            <?= e($errors['document_number']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Nombres -->
<div>
    <label for="first_name">Nombres</label>

    <input
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

<!-- Fecha de nacimiento -->
<div>
    <label for="birth_date">Fecha de nacimiento</label>

    <input
        id="birth_date"
        name="birth_date"
        type="date"
        max="<?= e(date('Y-m-d')) ?>"
        value="<?= e($data['birth_date'] ?? '') ?>"
        required
    >

    <?php if (isset($errors['birth_date'])): ?>
        <small class="field-error">
            <?= e($errors['birth_date']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Sexo -->
<div>
    <label for="sex">Sexo</label>

    <select id="sex" name="sex" required>
        <option value="">Seleccione</option>

        <option
            value="F"
            <?= ($data['sex'] ?? '') === 'F' ? 'selected' : '' ?>
        >
            Femenino
        </option>

        <option
            value="M"
            <?= ($data['sex'] ?? '') === 'M' ? 'selected' : '' ?>
        >
            Masculino
        </option>

        <option
            value="O"
            <?= ($data['sex'] ?? '') === 'O' ? 'selected' : '' ?>
        >
            Otro / no informa
        </option>
    </select>

    <?php if (isset($errors['sex'])): ?>
        <small class="field-error">
            <?= e($errors['sex']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Teléfono -->
<div>
    <label for="phone">Teléfono</label>

    <input
        id="phone"
        name="phone"
        maxlength="30"
        value="<?= e($data['phone'] ?? '') ?>"
    >

    <?php if (isset($errors['phone'])): ?>
        <small class="field-error">
            <?= e($errors['phone']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Correo -->
<div>
    <label for="email">Correo</label>

    <input
        id="email"
        name="email"
        type="email"
        maxlength="150"
        value="<?= e($data['email'] ?? '') ?>"
    >

    <?php if (isset($errors['email'])): ?>
        <small class="field-error">
            <?= e($errors['email']) ?>
        </small>
    <?php endif; ?>
</div>

<!-- Botones -->
<div class="form-actions full-width">
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
        Guardar paciente
    </button>
</div>
```

</form>
