<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user-plus"></i> Crear Nuevo Usuario</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="?controller=usuario&action=store" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-user"></i> Nombre Completo
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Contraseña
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                        </div>

                        <div class="mb-3">
                            <label for="id_rol" class="form-label">
                                <i class="fas fa-user-tag"></i> Rol
                            </label>
                            <select class="form-select" id="id_rol" name="id_rol" required>
                                <option value="">Seleccionar rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol->getIdRol(); ?>"
                                            <?php echo (isset($_POST['id_rol']) && $_POST['id_rol'] == $rol->getIdRol()) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rol->getNombre()); ?>
                                        <?php if ($rol->getDescripcion()): ?>
                                            - <?php echo htmlspecialchars($rol->getDescripcion()); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="?controller=usuario&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Crear Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación del formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        if (password.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres.');
            return false;
        }
    });
</script>

<?php include 'layout/footer.php'; ?>