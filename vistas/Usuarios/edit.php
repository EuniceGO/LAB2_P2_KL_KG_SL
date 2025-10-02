<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user-edit"></i> Editar Usuario</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="?controller=usuario&action=update" method="POST">
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario->getIdUsuario(); ?>">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-user"></i> Nombre Completo
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                   value="<?php echo htmlspecialchars($usuario->getNombre()); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($usuario->getEmail()); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="id_rol" class="form-label">
                                <i class="fas fa-user-tag"></i> Rol
                            </label>
                            <select class="form-select" id="id_rol" name="id_rol" required>
                                <option value="">Seleccionar rol...</option>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?php echo $rol->getIdRol(); ?>"
                                            <?php echo ($usuario->getIdRol() == $rol->getIdRol()) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rol->getNombre()); ?>
                                        <?php if ($rol->getDescripcion()): ?>
                                            - <?php echo htmlspecialchars($rol->getDescripcion()); ?>
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Nota:</strong> Para cambiar la contraseña, usa la opción "Cambiar Contraseña" desde el perfil del usuario.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="?controller=usuario&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>