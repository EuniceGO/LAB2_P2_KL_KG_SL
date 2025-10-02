<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user"></i> Mi Perfil</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table">
                                <tr>
                                    <th><i class="fas fa-id-card"></i> ID:</th>
                                    <td><?php echo $usuario->getIdUsuario(); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-user"></i> Nombre:</th>
                                    <td><?php echo htmlspecialchars($usuario->getNombre()); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-envelope"></i> Email:</th>
                                    <td><?php echo htmlspecialchars($usuario->getEmail()); ?></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-user-tag"></i> Rol:</th>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo htmlspecialchars($rol->getNombre()); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($rol->getDescripcion()): ?>
                                <tr>
                                    <th><i class="fas fa-info-circle"></i> Descripción del Rol:</th>
                                    <td><?php echo htmlspecialchars($rol->getDescripcion()); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="?controller=usuario&action=dashboard" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Dashboard
                                </a>
                                <div>
                                    <a href="?controller=usuario&action=changePassword" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Cambiar Contraseña
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>