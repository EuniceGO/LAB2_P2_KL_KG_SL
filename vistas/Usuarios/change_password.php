<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-key"></i> Cambiar Contraseña</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form action="?controller=usuario&action=changePassword" method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                <i class="fas fa-lock"></i> Contraseña Actual
                            </label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">
                                <i class="fas fa-key"></i> Nueva Contraseña
                            </label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <div class="form-text">La contraseña debe tener al menos 6 caracteres.</div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-key"></i> Confirmar Nueva Contraseña
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="?controller=usuario&action=dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cambiar Contraseña
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
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (newPassword.length < 6) {
            e.preventDefault();
            alert('La nueva contraseña debe tener al menos 6 caracteres.');
            return false;
        }
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('Las contraseñas no coinciden.');
            return false;
        }
    });
</script>

<?php include 'layout/footer.php'; ?>