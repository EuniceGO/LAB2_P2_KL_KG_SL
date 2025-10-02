<?php include 'layout/menu.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-plus"></i> Crear Nuevo Rol</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="?controller=role&action=store" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-tag"></i> Nombre del Rol
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                                   placeholder="Ej: Administrador, Usuario, Moderador">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">
                                <i class="fas fa-align-left"></i> Descripción
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                                      placeholder="Describe las funciones y permisos de este rol..."><?php echo isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : ''; ?></textarea>
                            <div class="form-text">Opcional: Describe las responsabilidades de este rol.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="?controller=role&action=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Crear Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>