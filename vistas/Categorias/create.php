<?php include 'layout/menu.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-gradient bg-success text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Nueva Categoría</h4>
                </div>
                <div class="card-body p-4">
                    <form action="?c=categoria&a=store" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre"
                                   class="form-control" 
                                   placeholder="Ingrese el nombre de la categoría" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      class="form-control" 
                                      rows="4" 
                                      placeholder="Ingrese una breve descripción" 
                                      required></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="?c=categoria&a=index" class="btn btn-secondary">
                                ⬅️ Volver
                            </a>
                            <button type="submit" class="btn btn-success">
                                💾 Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
